<?php

$params = array();
$params['client_id'] = $session->client_id;
$params['response_type'] = 'code';
$params['redirect_uri'] = $session->callback_url;
$params['state'] = md5(time());

// save state
$session->state = $params['state'];

redirect($session->url_authorize, $params);