<?php
include ('tools.php');


/* -------------------------------------------
 * Setup session
 * ------------------------------------------- */
session_start();
include ('session.php');

$session = new Session();
foreach ($_POST as $key => $val) {
    $session->$key = $val;
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
$callback_url = $site_url . 'callback';


/* -------------------------------------------
 * Setup OAuth2
 * ------------------------------------------- */
include ('OAuth2.php');
$oauth = new OAuth2($session->client_id, $session->client_secret, $callback_url);


/* -------------------------------------------
 * POST action
 * ------------------------------------------- */
if (isset($_SERVER['PATH_INFO'])) {
    $action = str_replace('/', '', $_SERVER['PATH_INFO']);
} else {
    $action = '';
}

switch ($action) {
    case 'authorize' :
        $params = array();
        $params['client_id'] = $session->client_id;
        $params['response_type'] = 'code';
        $params['redirect_uri'] = $callback_url;
        redirect($session->url_authorize, $params);
        break;
    
    case 'callback' :
        $session->request_code = $_GET['code'];
        break;
    
    case 'request' :
        $params = array();
        $params['client_id'] = $session->client_id;
        $params['client_secret'] = $session->client_secret;
        $params['grant_type'] = 'authorization_code';
        $params['redirect_uri'] = $callback_url;
        $params['code'] = $session->request_code;
        
        $session->access_token_response = $oauth->getAccessToken($session->url_access_token, $params);
        $session->access_token = json_decode($session->access_token_response)->access_token;
        break;
    
    case 'api' :
        $params = array();
        $params['oauth_token'] = $session->access_token;
        $params['access_token'] = $session->access_token;
        $session->api_response = $oauth->fetch($session->api_endpoint, $params, $session->api_method);
        
        // test if json
        $json = json_decode($session->api_response);
        if ($json) {
            $session->api_response = indent($session->api_response);
        } 
       
        break;
    
    case 'clear' :
        foreach ($_SESSION as $key => $val) {
            unset($_SESSION[$key]);
        }
        redirect($site_url);
        break;
}


/* -------------------------------------------
 * Show view
 * ------------------------------------------- */
include ('view.php');