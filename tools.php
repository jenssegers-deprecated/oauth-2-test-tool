<?php

function redirect($path, $params = array()) {
	header('Location: ' . $path . '?' . http_build_query($params));
}