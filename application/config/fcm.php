<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| Firebase Cloud Messaging (HTTP v1) for the mobile app's push notifications.
|
| fcm_service_account: path to the Firebase service-account JSON key
|   (Firebase console > Project settings > Service accounts > Generate new private key).
|   Keep it OUTSIDE the web root and readable by the web server and cron user only.
|   If the file is missing, push is disabled and queued notifications are marked "skipped".
|   Override the path with the TWEEZZO_FCM_SERVICE_ACCOUNT environment variable.
|   Otherwise the first readable of these is used:
|     /etc/tweezzo/firebase-service-account.json
|     <folder above the web root>/firebase-service-account.json  (shared hosting without /etc access)
*/
$config['fcm_service_account'] = getenv('TWEEZZO_FCM_SERVICE_ACCOUNT') ?: (function () {
    $candidates = [
        '/etc/tweezzo/firebase-service-account.json',
        dirname(FCPATH) . '/firebase-service-account.json',
    ];
    foreach ($candidates as $path) {
        if (@is_readable($path)) return $path;
    }
    return $candidates[0];
})();

/*
| Android notification channel per notification type (data.type), with its sound
| (res/raw/<sound>.ogg in the app). Channel ids must match the Flutter app
| (lib/core/push.dart); the first entry is used for unknown types.
*/
$config['fcm_android_channels'] = [
    'ticket' => ['channel' => 'tweezzo_tickets', 'sound' => 'tweezzo_ticket'],
    'task'   => ['channel' => 'tweezzo_tasks',   'sound' => 'tweezzo_task'],
    'tasks'  => ['channel' => 'tweezzo_tasks',   'sound' => 'tweezzo_task'],
];
