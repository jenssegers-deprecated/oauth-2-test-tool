<?php

$params = array();
$params['client_id'] = $session->client_id;
$params['client_secret'] = $session->client_secret;
$params['grant_type'] = 'authorization_code';
$params['redirect_uri'] = $session->callback_url;
$params['code'] = $session->request_code;
$params['grant_type'] = 'authorization_code';

$session->access_token_response = $curl->exec($session->url_access_token, 'POST', $params);

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