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

if(!$session->callback_url) {
	$session->callback_url = $site_url . 'callback';
}


/* -------------------------------------------
 * Setup OAuth2
 * ------------------------------------------- */
include ('OAuth2.php');
$oauth = new OAuth2($session->client_id, $session->client_secret, $session->callback_url);


/* -------------------------------------------
 * POST action
 * ------------------------------------------- */
if (isset($_SERVER['PATH_INFO'])) {
    $action = str_replace('/', '', $_SERVER['PATH_INFO']);
} else {
    $action = '';
}

// malformed url
if ($pos = strpos($action, '&')) {
    $get = substr($action, $pos + 1);
    $action = substr($action, 0, $pos);
    parse_str($get, $get);
    $_GET = array_merge($_GET, $get);
    
    $warning = 'Malformed callback URL';
}

switch ($action) {
    case 'authorize' :
        $params = array();
        $params['client_id'] = $session->client_id;
        $params['response_type'] = 'code';
        $params['redirect_uri'] = $session->callback_url;
        
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
        $params['redirect_uri'] = $session->callback_url;
        $params['code'] = $session->request_code;
        
        $session->access_token_response = $oauth->getAccessToken($session->url_access_token, $params);
        
        if (!$session->access_token_response) {
            $error = 'No access token response';
        } else {
            $json = json_decode($session->access_token_response);
            
            if (!$json) {
                $error = 'Access token response was not JSON';
            } else {
            	$session->access_token_response = indent($session->access_token_response);
            	
                if (!isset($json->access_token)) {
                    $error = 'Could not find access token in response';
                } else {
                    $session->access_token = $json->access_token;
                }
            }
        }
        
        break;
    
    case 'api' :
    	if ($session->api_body) {
    		parse_str($session->api_body, $params);
    	} else {
    		$params = array();
    	}
    	
        $params['oauth_token'] = $session->access_token;
        $params['access_token'] = $session->access_token;
        $session->api_response = $oauth->fetch($session->api_endpoint, $params, $session->api_method);
        
        if (!$session->api_response) {
            $error = 'No api response';
        } else {
	        // test if json
	        $json = json_decode($session->api_response);
	        if ($json) {
	            $session->api_response = indent($session->api_response);
	        } else {
	            $warning = 'API response was not JSON';
	        }
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
 * Cleanup
 * ------------------------------------------- */
if ($action != 'api') {
	$session->api_response = '';
}


/* -------------------------------------------
 * Show view
 * ------------------------------------------- */
include ('view.php');