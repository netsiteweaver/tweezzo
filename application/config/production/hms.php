<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['hms'] = array(
	'app_url' => 'https://hms.netsiteweaver.com/',
	'api_token' => 'a7dc052b78dfb6a22477d2de3679d84767310b7f495c12ed',
	'timeout_seconds' => 15,
	// Validated sprints still unpaid: HMS reminds the client every N days (cron sendGoLivePaymentReminders)
	'payment_reminder_days' => 7,
);
