<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base controller for the mobile app JSON API (application/controllers/api/*).
 *
 * - Stateless: authenticated with "Authorization: Bearer <token>" (see api_tokens). The PHP
 *   session started in index.php is discarded, so nothing an API request does leaks into a
 *   browser session and a browser cookie never authenticates an API call.
 * - The existing models read the logged-in user from $_SESSION, so the principal's keys are
 *   filled in for the duration of the request only, the same way each portal's login does.
 * - Responses: {"ok":true,"data":...} or {"ok":false,"error":{"code":"...","message":"..."}}.
 *
 * Principals: staff (users, user_type regular), developer (users, user_type developer),
 * customer (customer_access). The users table is shared by every portal, so user lookups
 * always filter by user_type.
 */
class Api_Controller extends CI_Controller
{
    const TOKEN_TTL_DAYS = 90;

    /** Methods of the child controller callable without a token. */
    protected $publicMethods = [];

    /** ['type' => staff|developer|customer, 'id' => int, 'name', 'email', 'customer_id' (customers), 'user_level' (staff)] */
    protected $principal;
    protected $tokenRow;

    /** Parsed JSON body (if the request is JSON). */
    private $jsonBody = null;

    public function __construct()
    {
        parent::__construct();

        // Drop the native session without writing it: API requests are token-only.
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_abort();
        }
        $_SESSION = [];

        if (function_exists('header_remove')) {
            header_remove('Access-Control-Allow-Origin');
            header_remove('Set-Cookie');
        }
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        header('X-Content-Type-Options: nosniff');

        // Same DB session timezone as the web app (MY_Controller).
        $this->db->query("SET @@session.time_zone = \"+04:00\"");

        $this->load->helper(['general', 'url']);

        $method = $this->router->fetch_method();
        if (!in_array($method, $this->publicMethods, true)) {
            $this->authenticateRequest();
        }
    }

    // ------------------------------------------------------------------
    // Responses
    // ------------------------------------------------------------------

    protected function ok($data = null, $http = 200)
    {
        http_response_code($http);
        echo json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR);
        exit;
    }

    protected function fail($code, $message, $http = 400, $extra = [])
    {
        http_response_code($http);
        $error = array_merge(['code' => $code, 'message' => $message], $extra);
        echo json_encode(['ok' => false, 'error' => $error], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function notFound($what = 'Resource')
    {
        $this->fail('not_found', "$what not found.", 404);
    }

    protected function forbidden($message = "You don't have access to this.")
    {
        $this->fail('forbidden', $message, 403);
    }

    // ------------------------------------------------------------------
    // Request helpers
    // ------------------------------------------------------------------

    protected function requireMethod($verb)
    {
        if (strtolower($this->input->method()) !== strtolower($verb)) {
            header('Allow: ' . strtoupper($verb));
            $this->fail('method_not_allowed', 'Use ' . strtoupper($verb) . ' for this endpoint.', 405);
        }
    }

    /** Value from the JSON body, then POST, then GET. */
    protected function param($key, $default = null)
    {
        if ($this->jsonBody === null) {
            $this->jsonBody = [];
            $ct = (string) $this->input->get_request_header('Content-Type', true);
            if (stripos($ct, 'application/json') !== false) {
                $decoded = json_decode((string) $this->input->raw_input_stream, true);
                if (is_array($decoded)) {
                    $this->jsonBody = $decoded;
                }
            }
        }
        if (array_key_exists($key, $this->jsonBody)) {
            return $this->jsonBody[$key];
        }
        $v = $this->input->post($key);
        if ($v !== null) return $v;
        $v = $this->input->get($key);
        return $v !== null ? $v : $default;
    }

    protected function intParam($key, $default = 0)
    {
        $v = $this->param($key, null);
        return ($v === null || $v === '') ? $default : (int) $v;
    }

    protected function page()
    {
        return max(1, $this->intParam('page', 1));
    }

    protected function perPage($default = 25, $max = 100)
    {
        return min($max, max(1, $this->intParam('per_page', $default)));
    }

    protected static function isUuid($v)
    {
        return is_string($v) && preg_match('/^[0-9a-fA-F-]{8,40}$/', $v);
    }

    // ------------------------------------------------------------------
    // Authentication
    // ------------------------------------------------------------------

    private function bearerToken()
    {
        $candidates = [
            $this->input->get_request_header('Authorization', true),
            $this->input->server('HTTP_AUTHORIZATION'),
            $this->input->server('REDIRECT_HTTP_AUTHORIZATION'),
        ];
        foreach ($candidates as $h) {
            if (is_string($h) && preg_match('/^Bearer\s+([a-f0-9]{64})$/i', trim($h), $m)) {
                return strtolower($m[1]);
            }
        }
        return null;
    }

    private function authenticateRequest()
    {
        $token = $this->bearerToken();
        if (!$token) {
            $this->fail('unauthenticated', 'Please sign in.', 401);
        }
        $row = $this->db->get_where('api_tokens', ['token_hash' => hash('sha256', $token), 'revoked' => 0])->row();
        if (!$row || strtotime($row->expires_at) < time()) {
            $this->fail('unauthenticated', 'Your session has expired. Please sign in again.', 401);
        }

        $principal = $this->loadPrincipal($row->principal_type, (int) $row->principal_id);
        if (!$principal) {
            // Account deactivated since the token was issued.
            $this->db->where('id', (int) $row->id)->update('api_tokens', ['revoked' => 1]);
            $this->fail('unauthenticated', 'Your account is no longer active.', 401);
        }

        // Sliding expiry, written at most once an hour.
        if (empty($row->last_used_at) || strtotime($row->last_used_at) < time() - 3600) {
            $this->db->where('id', (int) $row->id)->update('api_tokens', [
                'last_used_at' => date('Y-m-d H:i:s'),
                'expires_at'   => date('Y-m-d H:i:s', strtotime('+' . self::TOKEN_TTL_DAYS . ' days')),
                'ip_address'   => $this->input->ip_address(),
            ]);
        }

        $this->tokenRow = $row;
        $this->principal = $principal;
        $this->fillSession($principal);
    }

    /** Active principal or null. Every lookup in users filters by user_type. */
    protected function loadPrincipal($type, $id)
    {
        if ($type === 'staff' || $type === 'developer') {
            $userType = $type === 'staff' ? 'regular' : 'developer';
            $u = $this->db->select("users.id, username, users.name, photo, user_level, user_type, users.email, job_title, users.status, users.landing_page, department_id, dp.name AS department")
                ->from('users')
                ->join('departments dp', 'dp.id = users.department_id', 'left')
                ->where(['users.id' => (int) $id, 'users.user_type' => $userType, 'users.status' => 1])
                ->get()->row();
            if (!$u) return null;
            return [
                'type'       => $type,
                'id'         => (int) $u->id,
                'name'       => $u->name,
                'email'      => $u->email,
                'user_level' => $u->user_level,
                'photo'      => $u->photo,
                'row'        => $u,
            ];
        }
        if ($type === 'customer') {
            $ca = $this->db->select('ca.id, ca.name, ca.email, ca.customer_id, c.company_name')
                ->from('customer_access ca')
                ->join('customers c', 'c.customer_id = ca.customer_id')
                ->where(['ca.id' => (int) $id, 'ca.status' => 1, 'c.status' => 1, 'c.active' => 1])
                ->get()->row();
            if (!$ca) return null;
            return [
                'type'         => 'customer',
                'id'           => (int) $ca->id,
                'name'         => $ca->name,
                'email'        => $ca->email,
                'customer_id'  => (int) $ca->customer_id,
                'company_name' => $ca->company_name,
            ];
        }
        return null;
    }

    /** Request-only session keys, matching what each portal's login sets. */
    private function fillSession($p)
    {
        if ($p['type'] === 'staff') {
            $_SESSION['authenticated_user'] = $p['row'];
            $_SESSION['user_id'] = $p['id'];
            $_SESSION['user_name'] = $p['row']->username;
            $_SESSION['user_level'] = $p['row']->user_level;
            $_SESSION['department_name'] = $p['row']->department;
            $_SESSION['photo'] = $p['row']->photo;
        } elseif ($p['type'] === 'developer') {
            $_SESSION['developer_id'] = $p['id'];
            $_SESSION['developer_email'] = $p['email'];
            $_SESSION['developer_name'] = $p['name'];
        } elseif ($p['type'] === 'customer') {
            $_SESSION['customer_access_id'] = $p['id'];
            $_SESSION['customer_email'] = $p['email'];
            $_SESSION['customer_company_name'] = $p['company_name'];
            $_SESSION['customer_name'] = $p['name'];
        }
    }

    /** Creates a token for a principal; returns the plain token (only ever shown once). */
    protected function issueToken($type, $id, $deviceName)
    {
        $plain = bin2hex(random_bytes(32));
        $now = date('Y-m-d H:i:s');
        $this->db->insert('api_tokens', [
            'token_hash'     => hash('sha256', $plain),
            'principal_type' => $type,
            'principal_id'   => (int) $id,
            'device_name'    => mb_substr(trim((string) $deviceName), 0, 100) ?: null,
            'ip_address'     => $this->input->ip_address(),
            'created_at'     => $now,
            'last_used_at'   => $now,
            'expires_at'     => date('Y-m-d H:i:s', strtotime('+' . self::TOKEN_TTL_DAYS . ' days')),
        ]);
        return $plain;
    }

    // ------------------------------------------------------------------
    // Roles and permissions
    // ------------------------------------------------------------------

    protected function isStaff()     { return $this->principal['type'] === 'staff'; }
    protected function isDeveloper() { return $this->principal['type'] === 'developer'; }
    protected function isCustomer()  { return $this->principal['type'] === 'customer'; }

    /** Ticket actor in the shape Tickets_model expects. */
    protected function actor()
    {
        $type = $this->isStaff() ? 'admin' : $this->principal['type'];
        return ['type' => $type, 'id' => (int) $this->principal['id']];
    }

    /** Admin-portal permission (menu/permissions tables) for staff; false for everyone else. */
    protected function staffCan($controller, $action)
    {
        if (!$this->isStaff()) return false;
        $this->load->model('accesscontrol_model');
        return (bool) $this->accesscontrol_model->authorised($controller, $action);
    }

    protected function requireStaffPermission($controller, $action)
    {
        if (!$this->staffCan($controller, $action)) {
            $this->forbidden();
        }
    }

    /** Public shape of the signed-in principal. */
    protected function principalPayload()
    {
        $p = $this->principal;
        $out = [
            'type'  => $p['type'],
            'id'    => $p['id'],
            'name'  => $p['name'],
            'email' => $p['email'],
        ];
        if ($p['type'] === 'customer') {
            $out['company_name'] = $p['company_name'];
        }
        if ($p['type'] === 'staff') {
            $out['user_level'] = $p['user_level'];
            $out['permissions'] = [
                'helpdesk_listing'=> $this->staffCan('helpdesk', 'listing'),
                'helpdesk_view'   => $this->staffCan('helpdesk', 'view'),
                'helpdesk_add'    => $this->staffCan('helpdesk', 'add'),
                'helpdesk_edit'   => $this->staffCan('helpdesk', 'edit'),
                'helpdesk_delete' => $this->staffCan('helpdesk', 'delete'),
                'tasks_listing'   => $this->staffCan('tasks', 'listing'),
                'tasks_view'      => $this->staffCan('tasks', 'view'),
                'tasks_edit'      => $this->staffCan('tasks', 'add'),
            ];
        }
        return $out;
    }
}
