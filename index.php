<?php
include ('system/tools.php');
include ('system/curl.php');
$curl = new curl();

/* -------------------------------------------
 * Setup session
 * ------------------------------------------- */
session_start();
include ('system/session.php');
$session = new Session();
foreach ($_POST as $key => $val) {
    $session->$key = $val;
}

/* -------------------------------------------
 * Load config values
 * ------------------------------------------- */
include ('config.php');
if (isset($config) && is_array($config)) {
    foreach ($config as $key => $val) {
        if (!$session->$key) {
            $session->$key = $val;
        }
    }
}

/* -------------------------------------------
 * Detect callback url
 * ------------------------------------------- */
if (isset($_SERVER['HTTP_HOST'])) {
    $site_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
    $site_url .= '://' . $_SERVER['HTTP_HOST'];
    $site_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
} else {
    $site_url = 'http://localhost/';
}

if (!$session->callback_url) {
    $session->callback_url = $site_url . 'callback';
}

/* -------------------------------------------
 * Detect action
 * ------------------------------------------- */
if (isset($_SERVER['PATH_INFO'])) {
    $action = str_replace('/', '', $_SERVER['PATH_INFO']);
} else {
    $action = 'index';
}

/* -------------------------------------------
 * Warn if malformed url
 * ------------------------------------------- */
if ($pos = strpos($action, '&')) {
    $get = substr($action, $pos + 1);
    $action = substr($action, 0, $pos);
    parse_str($get, $get);
    $_GET = array_merge($_GET, $get);
    
    $warning = 'Malformed callback URL';
}

/* -------------------------------------------
 * Load action file
 * ------------------------------------------- */
if (file_exists('system/actions/' . $action . '.php')) {
    include ('system/actions/' . $action . '.php');
} else {
    include ('system/actions/default.php');
}

/* -------------------------------------------
 * Cleanup
 * ------------------------------------------- */
if ($action != 'api') {
    $session->api_response = '';
}

/* -------------------------------------------
 * Show view
 * ------------------------------------------- */
include ('view.php');

/* -------------------------------------------
 * Clear flash messages
 * ------------------------------------------- */
$session->clear_flash();