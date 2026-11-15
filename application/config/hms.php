<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| Link to HMS (quotations & invoicing): approved quotations are pulled from its api/tweezzo endpoints.
|
| api_token is a shared secret and is NOT stored in this repository. It must match the token HMS reads
| (HMS application/config/tweezzo.php). It is read from, in order:
|   1. the TWEEZZO_HMS_API_TOKEN environment variable
|   2. the file /etc/tweezzo/hms_api_token (own server)
|   3. the file ~/.secrets/tweezzo_hms_api_token, in the home folder of the account PHP runs as (shared hosting)
| Files: token on the first line. Never put the file inside a web-served folder (public_html, or /var/www/html on
| a dev box). Make it readable by the web server and cron users only (chmod 640, or 600 on shared hosting).
| Crons run through the PHP CLI, so an Apache SetEnv alone is not enough: prefer a file.
| No token found = the HMS link is disabled (Hms_model::isConfigured() returns false).
*/
if (!function_exists('tweezzo_hms_api_token')) {
	function tweezzo_hms_api_token()
	{
		$env = getenv('TWEEZZO_HMS_API_TOKEN');
		if (is_string($env) && trim($env) !== '') {
			return trim($env);
		}
		$paths = array('/etc/tweezzo/hms_api_token');
		// Home folder: HOME is often unset under the web server, so also ask the OS, then guess from the path (/home/<account>/...)
		$home = getenv('HOME');
		if ((!is_string($home) || $home === '') && function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
			$pw = @posix_getpwuid(posix_geteuid());
			$home = isset($pw['dir']) ? $pw['dir'] : '';
		}
		if ((!is_string($home) || $home === '') && preg_match('#^(/home\d*/[^/]+)/#', FCPATH, $m)) {
			$home = $m[1];
		}
		$docroot = isset($_SERVER['DOCUMENT_ROOT']) ? rtrim((string) $_SERVER['DOCUMENT_ROOT'], '/') : '';
		// ... but never from inside the web root (some servers use it as the web user's home)
		if (is_string($home) && $home !== '' && $home !== '/' && ($docroot === '' || strpos(rtrim($home, '/') . '/', $docroot . '/') !== 0)) {
			$paths[] = rtrim($home, '/') . '/.secrets/tweezzo_hms_api_token';
		}
		foreach ($paths as $path) {
			if (@is_readable($path)) {
				return trim((string) strtok((string) file_get_contents($path), "\r\n"));
			}
		}
		return '';
	}
}

$config['hms'] = array(
	'app_url' => 'http://hms.local/',
	'api_token' => tweezzo_hms_api_token(),
	'timeout_seconds' => 15,
	// Validated sprints still unpaid: HMS reminds the client every N days (cron sendGoLivePaymentReminders)
	'payment_reminder_days' => 7,
);
