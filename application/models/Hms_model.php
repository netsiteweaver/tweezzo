<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Client for the HMS api/tweezzo endpoints (application/config/hms.php).
 * Every call returns ['ok' => bool, 'error' => string, 'data' => array].
 */
class Hms_model extends CI_Model
{
    private $cfg;

    public function __construct()
    {
        parent::__construct();
        $this->config->load('hms', TRUE);
        $this->cfg = (array) $this->config->item('hms', 'hms');
    }

    public function isConfigured()
    {
        return !empty($this->cfg['app_url']) && !empty($this->cfg['api_token']);
    }

    /** Approved quotations, newest first. */
    public function quotations($pendingOnly = false)
    {
        return $this->request('GET', 'quotations' . ($pendingOnly ? '?pending=1' : ''));
    }

    /** One approved quotation with its line items. */
    public function quotation($uuid)
    {
        return $this->request('GET', 'quotation/' . rawurlencode((string) $uuid));
    }

    /** Tell HMS how many tasks were created, so the quotation shows as sent. */
    public function markExported($uuid, $taskCount)
    {
        return $this->request('POST', 'exported', ['uuid' => (string) $uuid, 'task_count' => (int) $taskCount]);
    }

    /**
     * The Tweezzo customer an HMS client most likely is: same email first, then same company name
     * ("Zordi Deal Ltd" and "ZordiDeal" count as the same). 0 when nothing matches.
     */
    public function matchCustomer(array $client)
    {
        $customers = $this->db->select('customer_id, company_name, email')
            ->from('customers')
            ->where(['status' => '1', 'active' => '1'])
            ->get()->result();

        $email = strtolower(trim((string) ($client['email'] ?? '')));
        if ($email !== '') {
            foreach ($customers as $c) {
                if (strtolower(trim((string) $c->email)) === $email) {
                    return (int) $c->customer_id;
                }
            }
        }

        $company = $this->companyKey($client['company'] ?? '');
        if ($company !== '') {
            foreach ($customers as $c) {
                if ($this->companyKey($c->company_name) === $company) {
                    return (int) $c->customer_id;
                }
            }
        }
        return 0;
    }

    /** Live tasks already carrying this quotation number as their reference. */
    public function importedTaskCount($quotationNumber)
    {
        return (int) $this->db->select('count(id) as ct')->from('tasks')
            ->where('ref', (string) $quotationNumber)
            ->where('source', 'hms')
            ->where('status', 1)
            ->get()->row('ct');
    }

    private function companyKey($name)
    {
        $name = strtolower((string) $name);
        $name = preg_replace('/\b(co|company|ltd|ltee|limited|inc|llc)\b\.?/', '', $name);
        return preg_replace('/[^a-z0-9]/', '', $name);
    }

    private function request($method, $path, $body = null)
    {
        if (!$this->isConfigured()) {
            return ['ok' => false, 'error' => 'HMS is not configured (application/config/hms.php).', 'data' => []];
        }

        $headers = ['Authorization: Bearer ' . $this->cfg['api_token'], 'Accept: application/json'];
        $ch = curl_init(rtrim($this->cfg['app_url'], '/') . '/api/tweezzo/' . $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, !empty($this->cfg['timeout_seconds']) ? (int) $this->cfg['timeout_seconds'] : 15);
        if ($method === 'POST') {
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            return ['ok' => false, 'error' => 'Could not reach HMS: ' . $curlError, 'data' => []];
        }
        $data = json_decode($response, true);
        if (!is_array($data)) {
            return ['ok' => false, 'error' => 'HMS returned an unexpected response (HTTP ' . $status . ').', 'data' => []];
        }
        if (empty($data['ok'])) {
            $reasons = [
                'unauthorized' => 'HMS rejected the API token. It must be the same in both applications.',
                'not_found' => 'Quotation not found in HMS.',
                'not_approved' => 'This quotation is not approved in HMS.',
            ];
            $code = isset($data['error']) ? (string) $data['error'] : '';
            return ['ok' => false, 'error' => isset($reasons[$code]) ? $reasons[$code] : 'HMS error: ' . ($code !== '' ? $code : 'HTTP ' . $status), 'data' => $data];
        }
        return ['ok' => true, 'error' => '', 'data' => $data];
    }
}
