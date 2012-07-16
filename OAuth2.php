<?php
/**
 * Implementing OAuth 2 for clients
 * Using cURL to make the requests
 * @author Lennart Martens <lennart at iRail.be>
 * 
 */

class OAuth2 {
    
    // private members
    private $client_id;
    private $client_secret;
    private $redirect_uri;
    private $last_response;
    
    /**
     * Construct with service specific credentials
     * @param string $client_id
     * @param string $client_secret
     * @param stirng $callback_url
     */
    public function __construct($client_id = FALSE, $client_secret = FALSE, $redirect_uri = FALSE) {

        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_uri = $redirect_uri;
    }
    
    /**
     * @param string $url
     * @param string $code
     * @param boolean $use_auth_headers
     * @return array or FALSE if failure. Still has to check on errorcodes.
     */
    public function getAccessToken($url, $params = array(), $use_auth_headers = FALSE) {
    	if(!isset($params['code'])){
    		return FALSE;
    	}

        $params['client_id'] = $this->client_id;
        $params['redirect_uri'] = $this->redirect_uri;
        $params['grant_type'] = 'authorization_code';
        
        if ($use_auth_headers) {
            // Not all OAuth2.0 providers accepts Basic Authentication header
            $auth_header = 'Basic ' . $this->client_secret;
            $response = $this->makeRequest($url,'post', $params, $auth_header);
        } else {
            $params['client_secret'] = $this->client_secret;
            $response = $this->makeRequest($url,'post', $params);
        }
        if (empty($response)) {
            return FALSE;
        }
        
        return $response;
    }
    
    /**
     * @return boolean
     */
    public function fetch($url, $params = array(), $method = 'get', $token_type = FALSE) {
    	if(!isset($params['access_token'])){
    		return FALSE;
    	}
        if ($token_type && preg_match('/bearer/i', $token_type)) {
            $auth_header = 'Bearer ' . $params['access_token'];
        } else {
            $auth_header = 'OAuth ' . $params['access_token'];
        }
        
        $this->last_response = $this->makeRequest($url, $method, $params, $auth_header);
        
        if (empty($this->last_response)) {
            return FALSE;
        }
        
        return $this->last_response;
    }
    
    /**
     * @return array
     */
    public function refreshAccessToken($url, $refresh_token) {
        $params = array('grant_type' => 'refresh_token', 'client_id' => $this->$client_id, 'refresh_token' => $refresh_token);
        
        $auth_header = 'Basic ' . $this->$client_secret;
        
        $json = $this->makeRequest($url, $params, $auth_header);
        $data = json_decode($json);
        
        return $data;
    }
    
    /**
     * @return string
     */
    public function getLastResponse() {
        return $this->last_response;
    }
    
    /**
     * CURL request
     * @param string $path
     * @param string $method
     * @param array $params
     * @param string $auth_header
     */
    private function makeRequest($path, $method = 'GET', $params = array(), $auth_header = FALSE) {
        $curl = curl_init();
        $params = http_build_query($params, NULL, '&');
        
        switch (strtoupper($method)) {
            case 'GET' :
                $path .= (strpos($path, '?') ? '&' : '?') . $params;
                curl_setopt($curl, CURLOPT_HTTPGET, TRUE);
                break;
            case 'POST' :
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                curl_setopt($curl, CURLOPT_POST, TRUE);
                break;
            case 'PUT' :
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                break;
            case 'DELETE' :
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
        }
        
        curl_setopt($curl, CURLOPT_URL, $path);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        if ( $auth_header !== FALSE ) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: ' . $auth_header));
        }
        
        $data = curl_exec($curl);
        
        // error
        if ($data === FALSE) {
            $errno = curl_errno($curl);
            $error = curl_error($curl);
            
            curl_close($curl);
            throw new Exception($error);
        }
        
        curl_close($curl);
        return $data;
    }
}