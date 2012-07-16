<?php

/**
 * EZ Session object
 */
class Session {
	
    function __get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : '';
    }
    
    function __set($key, $value) {
        $_SESSION[$key] = $value;
        return $this;
    }
    
}