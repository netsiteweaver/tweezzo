<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

/*$hook['pre_controller'][] = array(
        'class'    => 'Init',
        'function' => 'index',
        'filename' => 'Init.php',
        'filepath' => 'hooks'
);*/

$hook['pre_controller'][] = array(
        'class'    => 'Auth',
        'function' => 'check',
        'filename' => 'Auth.php',
        'filepath' => 'hooks'
);

// $hook['pre_system'][] = array(
//     'class'    => 'Maintenance',
//     'function' => 'offline_check',
//     'filename' => 'Maintenance.php',
//     'filepath' => 'hooks'
// );