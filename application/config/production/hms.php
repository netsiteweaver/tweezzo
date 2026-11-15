<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// api_token: see application/config/hms.php (env var or secret file, never this repository).
$config['hms'] = array(
	'app_url' => 'https://hms.netsiteweaver.com/',
	'api_token' => tweezzo_hms_api_token(),
	'timeout_seconds' => 15,
	// Validated sprints still unpaid: HMS reminds the client every N days (cron sendGoLivePaymentReminders)
	'payment_reminder_days' => 7,
);
