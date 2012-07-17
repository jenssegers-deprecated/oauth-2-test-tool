<?php

/**
 * EZ Session object
 * With support for flash messages
 */
class Session {
    
	public $flash;
	
	function __construct() {
		if(!isset($_SESSION['flash'])) {
			$_SESSION['flash'] = new stdClass();
		}
	}
	
    function __get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : '';
    }
    
    function __set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    function clear_flash() {
    	$_SESSION['flash'] = new stdClass();
    }

}