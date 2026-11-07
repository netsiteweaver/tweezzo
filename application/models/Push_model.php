<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Push notifications for the mobile app. Notifications are queued (push_queue) next to the
 * existing emails and sent by Cron::sendPush, so a slow FCM call never delays a web request.
 *
 * Queuing never throws: a push problem must not break the action or its email.
 */
class Push_model extends CI_Model
{
    const MAX_ATTEMPTS = 3;

    /** Queue for a users.id; the principal type comes from user_type (shared users table). */
    public function toUser($userId, $title, $body, array $data = [])
    {
        try {
            $type = $this->db->select('user_type')->get_where('users', ['id' => (int) $userId, 'status' => 1])->row('user_type');
            if ($type === 'regular') {
                $this->queue('staff', (int) $userId, $title, $body, $data);
            } elseif ($type === 'developer') {
                $this->queue('developer', (int) $userId, $title, $body, $data);
            }
        } catch (Throwable $e) {
            log_message('error', 'Push toUser failed: ' . $e->getMessage());
        }
    }

    /** Queue for a customer portal contact (customer_access.id). */
    public function toCustomerAccess($customerAccessId, $title, $body, array $data = [])
    {
        try {
            if (!empty($customerAccessId)) {
                $this->queue('customer', (int) $customerAccessId, $title, $body, $data);
            }
        } catch (Throwable $e) {
            log_message('error', 'Push toCustomerAccess failed: ' . $e->getMessage());
        }
    }

    /** Only queues when the principal has at least one device with push enabled. */
    public function queue($type, $id, $title, $body, array $data = [])
    {
        if (!$this->db->table_exists('push_queue')) return;
        $hasDevice = $this->db->where(['principal_type' => $type, 'principal_id' => (int) $id, 'push_enabled' => 1])
            ->count_all_results('device_tokens');
        if (!$hasDevice) return;

        $this->db->insert('push_queue', [
            'principal_type' => $type,
            'principal_id'   => (int) $id,
            'title'          => mb_substr(trim((string) $title), 0, 200),
            'body'           => mb_substr(trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $body), ENT_QUOTES, 'UTF-8'))), 0, 500),
            'data_json'      => json_encode($data),
            'status'         => 'pending',
            'created_at'     => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Sends pending notifications. Returns counters for the cron log.
     */
    public function processQueue($limit = 100)
    {
        $stats = ['sent' => 0, 'failed' => 0, 'skipped' => 0, 'removed_tokens' => 0];
        $this->load->library('fcm');

        // Recover rows left in "sending" by a crashed run.
        $this->db->where('status', 'sending')->where('created_at <', date('Y-m-d H:i:s', strtotime('-15 minutes')))
            ->update('push_queue', ['status' => 'pending']);

        $rows = $this->db->where('status', 'pending')->order_by('id')->limit((int) $limit)->get('push_queue')->result();
        foreach ($rows as $row) {
            // Claim the row so overlapping cron runs don't double-send.
            $this->db->where(['id' => (int) $row->id, 'status' => 'pending'])->update('push_queue', ['status' => 'sending']);
            if ($this->db->affected_rows() === 0) continue;

            if (!$this->fcm->isConfigured()) {
                $this->finish($row, 'skipped', 'FCM not configured');
                $stats['skipped']++;
                continue;
            }

            $devices = $this->db->get_where('device_tokens', [
                'principal_type' => $row->principal_type,
                'principal_id'   => (int) $row->principal_id,
                'push_enabled'   => 1,
            ])->result();
            if (empty($devices)) {
                $this->finish($row, 'skipped', 'No device');
                $stats['skipped']++;
                continue;
            }

            $data = json_decode((string) $row->data_json, true) ?: [];
            $anyOk = false;
            $lastError = null;
            foreach ($devices as $d) {
                $res = $this->fcm->send($d->fcm_token, $row->title, $row->body, $data);
                if ($res['ok']) {
                    $anyOk = true;
                } elseif ($res['unregistered']) {
                    $this->db->where('id', (int) $d->id)->delete('device_tokens');
                    $stats['removed_tokens']++;
                } else {
                    $lastError = $res['error'];
                }
            }

            if ($anyOk || $lastError === null) {
                $this->finish($row, $anyOk ? 'sent' : 'skipped', $lastError);
                $stats[$anyOk ? 'sent' : 'skipped']++;
            } elseif ((int) $row->attempts + 1 >= self::MAX_ATTEMPTS) {
                $this->finish($row, 'failed', $lastError);
                $stats['failed']++;
            } else {
                $this->db->where('id', (int) $row->id)->update('push_queue', [
                    'status' => 'pending', 'attempts' => (int) $row->attempts + 1, 'last_error' => mb_substr((string) $lastError, 0, 500),
                ]);
            }
        }

        // Housekeeping: keep 30 days of history.
        $this->db->where_in('status', ['sent', 'skipped', 'failed'])
            ->where('created_at <', date('Y-m-d H:i:s', strtotime('-30 days')))->delete('push_queue');

        return $stats;
    }

    private function finish($row, $status, $error)
    {
        $this->db->where('id', (int) $row->id)->update('push_queue', [
            'status'     => $status,
            'attempts'   => (int) $row->attempts + 1,
            'last_error' => $error !== null ? mb_substr((string) $error, 0, 500) : null,
            'sent_at'    => $status === 'sent' ? date('Y-m-d H:i:s') : null,
        ]);
    }
}
