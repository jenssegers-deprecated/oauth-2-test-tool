<?php

$params = array();
$params['client_id'] = $session->client_id;
$params['response_type'] = 'code';
$params['redirect_uri'] = $session->callback_url;

redirect($session->url_authorize, $params);