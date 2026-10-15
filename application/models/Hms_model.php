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

    /**
     * What the client still owes on the HMS documents (QUO/EST/INV numbers in `ref`) of every task in the sprint.
     * Returns refs (the numbers found), error (HMS could not be asked), currency, invoices and quotations still due.
     */
    public function sprintOutstanding($sprintId)
    {
        $refs = $this->sprintRefs($sprintId);
        if (empty($refs)) {
            return $this->outstandingResult($refs, ['ok' => true, 'error' => '', 'data' => []]);
        }
        $res = $this->request('GET', 'outstanding?refs=' . rawurlencode(implode(',', $refs)));
        if (!$res['ok']) {
            log_message('error', 'HMS outstanding check for sprint ' . (int) $sprintId . ': ' . $res['error']);
        }
        return $this->outstandingResult($refs, $res);
    }

    /**
     * Same as sprintOutstanding(), but HMS also emails the client's billing addresses that payment must be done
     * before going live (nothing is sent when all is paid). `emails` lists, per client, who was emailed or failed.
     */
    public function sprintPaymentRequired($sprintId, $projectName, $sprintName, $reminder = false)
    {
        $refs = $this->sprintRefs($sprintId);
        if (empty($refs)) {
            return $this->outstandingResult($refs, ['ok' => true, 'error' => '', 'data' => []]);
        }
        $res = $this->request('POST', 'payment_required', [
            'refs' => $refs,
            'project' => (string) $projectName,
            'sprint' => (string) $sprintName,
            'reminder' => (bool) $reminder,
        ]);
        if (!$res['ok']) {
            log_message('error', 'HMS payment notice for sprint ' . (int) $sprintId . ': ' . $res['error']);
        }
        return $this->outstandingResult($refs, $res);
    }

    /** HMS document numbers (QUO/EST/INV) in the `ref` of the sprint's live tasks. */
    private function sprintRefs($sprintId)
    {
        $refs = [];
        $rows = $this->db->select('ref')->from('tasks')
            ->where('sprint_id', (int) $sprintId)
            ->where('status', 1)
            ->where("COALESCE(ref,'') !=", '')
            ->get()->result();
        foreach ($rows as $row) {
            if (preg_match_all('/\b(?:QUO|EST|INV)\d+\b/i', (string) $row->ref, $m)) {
                foreach ($m[0] as $ref) {
                    $refs[strtoupper($ref)] = strtoupper($ref);
                }
            }
        }
        return array_values($refs);
    }

    private function outstandingResult($refs, $res)
    {
        $data = $res['ok'] ? $res['data'] : [];
        return [
            'refs' => $refs,
            'error' => $res['ok'] ? '' : $res['error'],
            'currency' => (string) ($data['currency'] ?? ''),
            'invoices' => (array) ($data['invoices'] ?? []),
            'quotations' => (array) ($data['quotations'] ?? []),
            'emails' => (array) ($data['emails'] ?? []),
        ];
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
