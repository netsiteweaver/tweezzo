<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/Api_Controller.php';

/**
 * POST api/account/login            {email, password, portal: team|customer, customer_id?, device_name?}
 * POST api/account/logout
 * GET  api/account/me
 * POST api/account/registerDevice   {fcm_token, platform?}
 * POST api/account/unregisterDevice {fcm_token}
 * POST api/account/pushSettings     {fcm_token, push_enabled: 0|1}
 */
class Account extends Api_Controller
{
    protected $publicMethods = ['login'];

    const MAX_FAILED_PER_IP = 20;
    const MAX_FAILED_PER_EMAIL = 8;
    const FAILED_WINDOW_MINUTES = 15;

    public function login()
    {
        $this->requireMethod('post');

        $email = trim((string) $this->param('email', ''));
        $password = (string) $this->param('password', '');
        $portal = $this->param('portal', 'team') === 'customer' ? 'customer' : 'team';
        $deviceName = (string) $this->param('device_name', '');

        if ($email === '' || $password === '') {
            $this->fail('validation', 'Please enter your email and password.', 422);
        }
        if ($this->rateLimited($email)) {
            $this->fail('rate_limited', 'Too many failed attempts. Please wait a few minutes and try again.', 429);
        }

        if ($portal === 'customer') {
            $this->loginCustomer($email, $password, $deviceName);
        } else {
            $this->loginTeam($email, $password, $deviceName);
        }
    }

    /** Admin-portal staff and developers share the users table; user_type decides the principal. */
    private function loginTeam($email, $password, $deviceName)
    {
        $this->load->model('users_model');
        $user = $this->users_model->authenticate(['inputEmail' => $email, 'inputPassword' => $password]);
        if (!$user) {
            $this->recordAttempt($email, false);
            $this->fail('invalid_credentials', 'Invalid email or password.', 401);
        }
        if ((string) $user->status !== '1') {
            $this->recordAttempt($email, false);
            $this->fail('inactive', (string) $user->status === '2'
                ? 'Your account is currently inactive. Please contact the administrator.'
                : 'This account has been deleted.', 403);
        }
        if ($user->user_type === 'regular') {
            $type = 'staff';
        } elseif ($user->user_type === 'developer') {
            $type = 'developer';
        } else {
            $this->recordAttempt($email, false);
            $this->fail('invalid_credentials', 'Please sign in as a customer.', 401);
        }

        $this->recordAttempt($email, true);
        $this->respondWithToken($type, (int) $user->id, $deviceName);
    }

    private function loginCustomer($email, $password, $deviceName)
    {
        $this->load->model('Customersportal_model');
        $customerId = $this->intParam('customer_id', 0);
        $result = $this->Customersportal_model->authenticate([
            'email'       => $email,
            'password'    => $password,
            'customer_id' => $customerId ?: null,
        ]);

        if (!empty($result['customers'])) {
            // Same login for several companies: the app asks which one and retries with customer_id.
            $this->fail('choose_customer', 'Please choose the company to sign in to.', 409, [
                'customers' => array_map(function ($c) {
                    return ['customer_id' => (int) $c->customer_id, 'company_name' => $c->company_name];
                }, $result['customers']),
            ]);
        }
        if ($result['result'] !== true || empty($result['user'][0])) {
            $this->recordAttempt($email, false);
            $this->fail('invalid_credentials', 'Invalid email or password.', 401);
        }

        $this->recordAttempt($email, true);
        $this->respondWithToken('customer', (int) $result['user'][0]->customer_access_id, $deviceName);
    }

    private function respondWithToken($type, $id, $deviceName)
    {
        $this->principal = $this->loadPrincipal($type, $id);
        if (!$this->principal) {
            $this->fail('inactive', 'Your account is not active.', 403);
        }
        $token = $this->issueToken($type, $id, $deviceName);
        $this->ok([
            'token'      => $token,
            'expires_in' => self::TOKEN_TTL_DAYS * 86400,
            'user'       => $this->principalPayloadForLogin(),
        ]);
    }

    /** principalPayload() needs the request session keys for staff permission checks. */
    private function principalPayloadForLogin()
    {
        if ($this->principal['type'] === 'staff') {
            $_SESSION['user_id'] = $this->principal['id'];
            $_SESSION['user_level'] = $this->principal['user_level'];
        }
        return $this->principalPayload();
    }

    private function rateLimited($email)
    {
        $since = date('Y-m-d H:i:s', time() - self::FAILED_WINDOW_MINUTES * 60);
        $byIp = $this->db->where(['ip_address' => $this->input->ip_address(), 'success' => 0, 'created_at >' => $since])
            ->count_all_results('api_login_attempts');
        if ($byIp >= self::MAX_FAILED_PER_IP) return true;
        $byEmail = $this->db->where(['email' => mb_strtolower($email), 'success' => 0, 'created_at >' => $since])
            ->count_all_results('api_login_attempts');
        return $byEmail >= self::MAX_FAILED_PER_EMAIL;
    }

    private function recordAttempt($email, $success)
    {
        $this->db->insert('api_login_attempts', [
            'ip_address' => $this->input->ip_address(),
            'email'      => mb_substr(mb_strtolower($email), 0, 150),
            'success'    => $success ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        // Keep the table small.
        if (mt_rand(1, 50) === 1) {
            $this->db->where('created_at <', date('Y-m-d H:i:s', strtotime('-7 days')))->delete('api_login_attempts');
        }
    }

    public function logout()
    {
        $this->requireMethod('post');
        $this->db->where('id', (int) $this->tokenRow->id)->update('api_tokens', ['revoked' => 1]);
        $this->db->where('api_token_id', (int) $this->tokenRow->id)->delete('device_tokens');
        $this->ok(['signed_out' => true]);
    }

    public function me()
    {
        $this->ok($this->principalPayload());
    }

    public function registerDevice()
    {
        $this->requireMethod('post');
        $fcm = trim((string) $this->param('fcm_token', ''));
        if ($fcm === '' || strlen($fcm) > 255) {
            $this->fail('validation', 'Missing or invalid fcm_token.', 422);
        }
        $platform = in_array($this->param('platform'), ['android', 'ios'], true) ? $this->param('platform') : 'android';
        $now = date('Y-m-d H:i:s');

        // A device token belongs to whoever signed in on that device last.
        $existing = $this->db->get_where('device_tokens', ['fcm_token' => $fcm])->row();
        $row = [
            'api_token_id'   => (int) $this->tokenRow->id,
            'principal_type' => $this->principal['type'],
            'principal_id'   => (int) $this->principal['id'],
            'platform'       => $platform,
            'updated_at'     => $now,
        ];
        if ($existing) {
            $this->db->where('id', (int) $existing->id)->update('device_tokens', $row);
        } else {
            $row['fcm_token'] = $fcm;
            $row['push_enabled'] = 1;
            $row['created_at'] = $now;
            $this->db->insert('device_tokens', $row);
        }
        $this->ok(['registered' => true]);
    }

    public function unregisterDevice()
    {
        $this->requireMethod('post');
        $this->db->where([
            'fcm_token'      => (string) $this->param('fcm_token', ''),
            'principal_type' => $this->principal['type'],
            'principal_id'   => (int) $this->principal['id'],
        ])->delete('device_tokens');
        $this->ok(['unregistered' => true]);
    }

    public function pushSettings()
    {
        $this->requireMethod('post');
        $enabled = (int) (bool) $this->intParam('push_enabled', 1);
        $this->db->where([
            'fcm_token'      => (string) $this->param('fcm_token', ''),
            'principal_type' => $this->principal['type'],
            'principal_id'   => (int) $this->principal['id'],
        ])->update('device_tokens', ['push_enabled' => $enabled, 'updated_at' => date('Y-m-d H:i:s')]);
        $this->ok(['push_enabled' => (bool) $enabled]);
    }
}
