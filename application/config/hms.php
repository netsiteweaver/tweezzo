<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Link to HMS (quotations & invoicing): approved quotations are pulled from its api/tweezzo endpoints.
// api_token must match $config['tweezzo']['api_token'] in HMS's application/config/tweezzo.php
$config['hms'] = array(
	'app_url' => 'http://hms.local/',
	'api_token' => '3d8d93cafc47b59e60ee613a8147ca29eb69458fd2fd12ae',
	'timeout_seconds' => 15,
);
