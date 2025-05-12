<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['mailer_type'] = 'remote'; // possible values are remote or local

// The below parameters are requied only if $config['mailer_type'] = 'remote'
$config['mailer_endpoint']	= 'http://voxmail.xyz/api/v1.0/';
$config['token'] = 'TOKEN_HERE';