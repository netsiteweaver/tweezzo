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
*/
$config['fcm_service_account'] = getenv('TWEEZZO_FCM_SERVICE_ACCOUNT') ?: '/etc/tweezzo/firebase-service-account.json';

/* Android notification channel id the app creates (must match the Flutter app). */
$config['fcm_android_channel'] = 'tweezzo_default';
