<?php

if ($session->api_body) {
    parse_str($session->api_body, $params);
} else {
    $params = array();
}

$params['oauth_token'] = $session->access_token;
$params['access_token'] = $session->access_token;

$session->api_response = $curl->exec($session->api_endpoint, $session->api_method, $params);

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