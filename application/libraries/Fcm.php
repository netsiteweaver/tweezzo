<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Minimal Firebase Cloud Messaging HTTP v1 client (no Composer): signs a service-account JWT
 * with openssl, exchanges it for an OAuth access token (cached ~55 min), and sends messages.
 */
class Fcm
{
    const SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';
    const TOKEN_URL = 'https://oauth2.googleapis.com/token';

    private $account;
    /** @var array<string,array{channel:string,sound:string}> data.type => channel + sound */
    private $channels;
    private $cacheFile;
    private $accessToken;

    public function __construct()
    {
        $CI =& get_instance();
        $CI->config->load('fcm', true);
        $path = (string) $CI->config->item('fcm_service_account', 'fcm');
        $channels = $CI->config->item('fcm_android_channels', 'fcm');
        $this->channels = is_array($channels) && $channels
            ? $channels
            : ['ticket' => ['channel' => 'tweezzo_tickets', 'sound' => 'tweezzo_ticket']];
        $this->cacheFile = APPPATH . 'cache/fcm_access_token.json';

        if ($path !== '' && is_readable($path)) {
            $json = json_decode((string) file_get_contents($path), true);
            if (!empty($json['client_email']) && !empty($json['private_key']) && !empty($json['project_id'])) {
                $this->account = $json;
            }
        }
    }

    public function isConfigured()
    {
        return !empty($this->account);
    }

    /**
     * @return array ['ok' => bool, 'unregistered' => bool, 'error' => string|null]
     */
    public function send($deviceToken, $title, $body, array $data = [])
    {
        if (!$this->isConfigured()) {
            return ['ok' => false, 'unregistered' => false, 'error' => 'FCM is not configured'];
        }
        $token = $this->accessToken();
        if (!$token) {
            return ['ok' => false, 'unregistered' => false, 'error' => 'Unable to get an FCM access token'];
        }

        // FCM data values must be strings.
        $data = array_map('strval', array_filter($data, function ($v) { return $v !== null; }));
        $type = isset($data['type']) ? (string) $data['type'] : '';
        $ch = isset($this->channels[$type]) ? $this->channels[$type] : reset($this->channels);

        $message = [
            'message' => [
                'token'        => $deviceToken,
                'notification' => ['title' => $title, 'body' => $body],
                'data'         => (object) $data,
                'android'      => [
                    'priority'     => 'HIGH',
                    // sound: used by Android 7 and older (no channels); newer versions play the channel's sound.
                    'notification' => ['channel_id' => $ch['channel'], 'sound' => $ch['sound'], 'tag' => $data['tag'] ?? null],
                ],
            ],
        ];
        if ($message['message']['android']['notification']['tag'] === null) {
            unset($message['message']['android']['notification']['tag']);
        }

        $url = 'https://fcm.googleapis.com/v1/projects/' . rawurlencode($this->account['project_id']) . '/messages:send';
        list($status, $response) = $this->post($url, json_encode($message), [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
        ]);
        if ($status === 200) {
            return ['ok' => true, 'unregistered' => false, 'error' => null];
        }
        $err = json_decode((string) $response, true);
        $code = $err['error']['details'][0]['errorCode'] ?? ($err['error']['status'] ?? '');
        $unregistered = in_array($code, ['UNREGISTERED', 'INVALID_ARGUMENT', 'NOT_FOUND', 'SENDER_ID_MISMATCH'], true) || $status === 404;
        return ['ok' => false, 'unregistered' => $unregistered, 'error' => "HTTP $status $code"];
    }

    private function accessToken()
    {
        if ($this->accessToken) return $this->accessToken;

        if (is_readable($this->cacheFile)) {
            $cached = json_decode((string) file_get_contents($this->cacheFile), true);
            if (!empty($cached['token']) && ($cached['expires'] ?? 0) > time() + 60
                && ($cached['client'] ?? '') === $this->account['client_email']) {
                return $this->accessToken = $cached['token'];
            }
        }

        $now = time();
        $header = self::b64(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $claims = self::b64(json_encode([
            'iss'   => $this->account['client_email'],
            'scope' => self::SCOPE,
            'aud'   => self::TOKEN_URL,
            'iat'   => $now,
            'exp'   => $now + 3600,
        ]));
        $signature = '';
        if (!openssl_sign("$header.$claims", $signature, $this->account['private_key'], OPENSSL_ALGO_SHA256)) {
            log_message('error', 'FCM: unable to sign JWT');
            return null;
        }
        $jwt = "$header.$claims." . self::b64($signature);

        list($status, $response) = $this->post(self::TOKEN_URL, http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]), ['Content-Type: application/x-www-form-urlencoded']);
        $json = json_decode((string) $response, true);
        if ($status !== 200 || empty($json['access_token'])) {
            log_message('error', 'FCM: token exchange failed: HTTP ' . $status);
            return null;
        }

        $expires = $now + (int) ($json['expires_in'] ?? 3600) - 300;
        @file_put_contents($this->cacheFile, json_encode([
            'token' => $json['access_token'], 'expires' => $expires, 'client' => $this->account['client_email'],
        ]), LOCK_EX);
        @chmod($this->cacheFile, 0600);
        return $this->accessToken = $json['access_token'];
    }

    private function post($url, $body, array $headers)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT        => 15,
        ]);
        $response = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return [$status, $response];
    }

    private static function b64($s)
    {
        return rtrim(strtr(base64_encode($s), '+/', '-_'), '=');
    }
}
