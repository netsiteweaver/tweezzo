<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * A staff user's API tokens, for Settings > Tokens: personal tokens they create for the Yawmee desktop
 * app, and the mobile app sign-ins (api/account/login). Same api_tokens rows and rules as Api_Controller:
 * 64 hex characters, only the SHA-256 stored, TTL_DAYS sliding expiry.
 */
class Api_tokens_model extends CI_Model
{
    const TTL_DAYS = 90;        // Api_Controller::TOKEN_TTL_DAYS
    const MAX_ACTIVE = 20;

    /** Creates a staff token and returns it in plain text (the only time it is available), or false. */
    public function issue($userId, $label, $ip)
    {
        if (count($this->listForStaff($userId)) >= self::MAX_ACTIVE) {
            return false;
        }
        $plain = bin2hex(random_bytes(32));
        $now = date('Y-m-d H:i:s');
        $this->db->insert('api_tokens', [
            'token_hash'     => hash('sha256', $plain),
            'principal_type' => 'staff',
            'principal_id'   => (int) $userId,
            'device_name'    => mb_substr(trim((string) $label), 0, 100) ?: null,
            'ip_address'     => mb_substr((string) $ip, 0, 45),
            'created_at'     => $now,
            'last_used_at'   => null,
            'expires_at'     => date('Y-m-d H:i:s', strtotime('+' . self::TTL_DAYS . ' days')),
        ]);
        return $plain;
    }

    /** The user's tokens that still work, newest first (never the hash). */
    public function listForStaff($userId)
    {
        return $this->db->select('id, device_name, created_at, last_used_at, ip_address, expires_at')
            ->from('api_tokens')
            ->where(['principal_type' => 'staff', 'principal_id' => (int) $userId, 'revoked' => 0])
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->order_by('id', 'desc')
            ->get()->result();
    }

    /** Revokes one of the user's own tokens. Returns whether one was revoked. */
    public function revoke($id, $userId)
    {
        $this->db->where(['id' => (int) $id, 'principal_type' => 'staff', 'principal_id' => (int) $userId, 'revoked' => 0])
            ->update('api_tokens', ['revoked' => 1]);
        return $this->db->affected_rows() > 0;
    }
}
