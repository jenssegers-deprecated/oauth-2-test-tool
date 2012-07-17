<?php
/**
 * EZ curl object
 */

class curl {
    
    public $response = '';
    public $header = '';
    public $error = FALSE;
    public $url = '';
    
    /**
     * CURL request
     * @param string $path
     * @param string $method
     * @param array $params
     * @param string $auth_header
     */
    function exec($path, $method = 'GET', $params = array(), $auth_header = FALSE) {
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
        
        $this->url = $path;
        curl_setopt($curl, CURLOPT_URL, $path);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        
        if ($auth_header !== FALSE) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $auth_header);
        }
        
        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        
        if ($info['download_content_length'] <= 0) {
            $info['download_content_length'] = $info['size_download'];
        }
        
        $this->header = substr($response, 0, $info['header_size']);
        $this->response = substr($response, -$info['download_content_length']);
        
        
        if ($this->response === FALSE) {
            $this->error = curl_error($curl);
            curl_close($curl);
            return FALSE;
        }
        
        curl_close($curl);
        return $this->response;
    }
}