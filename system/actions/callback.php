<?php

if (!isset($_GET['state'])) {
    $warning = 'Did not receive the state parameter';
} else {
    if ($_GET['state'] != $session->state) {
        $warning = 'State parameter did not match';
    } else {
    	$info = 'State parameter matches';
    }
}

if (!isset($_GET['code'])) {
    $error = 'Did not receive a request code';
} else {
    $session->request_code = $_GET['code'];
}
    