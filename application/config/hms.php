<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| Link to HMS (quotations & invoicing): approved quotations are pulled from its api/tweezzo endpoints.
|
| api_token is a shared secret and is NOT stored in this repository. It must match the token HMS reads
| (HMS application/config/tweezzo.php). It is read from, in order:
|   1. the TWEEZZO_HMS_API_TOKEN environment variable
|   2. the file /etc/tweezzo/hms_api_token (token on the first line)
| Never put the file inside a web-served folder (on a dev box /var/www/html is one). Make it readable by the web
| server and cron users only (chmod 640).
| Crons run through the PHP CLI, so an Apache SetEnv alone is not enough: prefer the file.
| No token found = the HMS link is disabled (Hms_model::isConfigured() returns false).
*/
if (!function_exists('tweezzo_hms_api_token')) {
	function tweezzo_hms_api_token()
	{
		$env = getenv('TWEEZZO_HMS_API_TOKEN');
		if (is_string($env) && trim($env) !== '') {
			return trim($env);
		}
		$path = '/etc/tweezzo/hms_api_token';
		return @is_readable($path) ? trim((string) strtok((string) file_get_contents($path), "\r\n")) : '';
	}
}

$config['hms'] = array(
	'app_url' => 'http://hms.local/',
	'api_token' => tweezzo_hms_api_token(),
	'timeout_seconds' => 15,
	// Validated sprints still unpaid: HMS reminds the client every N days (cron sendGoLivePaymentReminders)
	'payment_reminder_days' => 7,
);
