<?php
require_once dirname(__FILE__) . '/rest_result.class.php';
require_once Path :: get_plugin_path() . 'pear/HTTP/Request.php';


/**
 * This class can be used to send REST requests to a REST service
 * 
 * The request may be sent by using the curl library if the mod_cul module is installed (default) or the HTTP_Request of the PEAR package.
 *  
 * If the curl library is used, some additional security features are available, 
 * such as sending a client certificate to authenticate the request  
 *
 */
class RestClient
{
    const MODE_CURL               = 'MODE_CURL';
    const MODE_PEAR               = 'MODE_PEAR';
    
    const REQUEST                 = 'request';
    const RESPONSE                = 'response';
    
    const RESPONSE_TARGET_URL     = 'RESULT_TARGET_URL';
    const RESPONSE_ERROR          = 'RESULT_ERROR';
    const RESPONSE_MIME           = 'RESULT_MIME';
    const RESPONSE_CONTENT        = 'RESULT_CONTENT';
    const RESPONSE_HTTP_CODE      = 'RESULT_HTTP_CODE';
    
    /**
    * The connexion mode to the REST service
    *
    * @var string
    */ 
    private $connexion_mode;
    
    /**
    * The URL of the REST service
    *
    * @var string
    */ 
    private $url = null;
    
    /**
    * the basic authentication login
    *
    * @var string
    */ 
    private $basic_login = null;
    
    /**
    * the basic authentication password
    *
    * @var string
    */ 
    private $basic_password = null;
    
    /**
    * the HTTP method used to send the request
    *
    * @var string
    */ 
    private $http_method = 'GET';
    
    /**
    * the data to send with the request. Typically used with the POST http method
    *
    * @var mixed
    */ 
    private $data_to_send = null;
    
    /**
    * client certificate path to use. The file may contain the certificate and the key as well.
    * The certificate format must be PEM.
    *
    * @var string
    */ 
    private $client_certificate_file;
    
    /**
    * client certificate key path
    *
    * @var string
    */ 
    private $client_certificate_key_file;
    
    /**
    * client certificate key password used for authentication
    *
    * @var string
    */ 
    private $client_certificate_key_password;
    
    /**
    * the checking of the target certificate
    *
    * @var bool
    */ 
    private $check_target_certificate = true;
    
    /**
    * the path to additional CA certificates used to verify the target certificate
    *
    * @var string
    */ 
    private $target_ca_file;
    
    /****************************************************************************************/
    
    public function RestClient()
    {
        $this->set_default_mode();
    }
    
    
    /****************************************************************************************/
    
    /**
     * Check if the cURL extension is installed. If not, the PEAR mode is used
     * 
     * @return void
     */
    private function set_default_mode()
    {
        /*
         * Test if the curl extension is loaded
         */
        if(extension_loaded('curl'))
        {
            $this->connexion_mode = self :: MODE_CURL;
        }
        else
        {
            $this->connexion_mode = self :: MODE_PEAR;
        }
    }
    
    /**
    * Get the connexion mode to the REST service
    *
    * @return string
    */
    public function get_connexion_mode()
    {
    	return $this->connexion_mode;
    }
    
    /**
    * Set the connexion mode to the REST service
    *
    * @var $connexion_mode string
    * @return void
    */
    public function set_connexion_mode($connexion_mode)
    {
        if($connexion_mode == self :: MODE_CURL || $connexion_mode == self :: MODE_PEAR)
        {
            $this->connexion_mode = connexion_mode;
        } 
        else
        {
            $this->set_default_mode();
        }
    }
    
    
    /****************************************************************************************/
    
    /**
    * Get The URL of the REST service
    *
    * @return string
    */
    public function get_url()
    {
    	return $this->url;
    }
    
    /**
    * Set The URL of the REST service
    *
    * @var $url string
    * @return void
    */
    public function set_url($url)
    {
    	$this->url = $url;
    }
   
    
    /****************************************************************************************/
    
    /**
    * Get the basic authentication login
    *
    * @return string
    */
    public function get_basic_login()
    {
    	return $this->basic_login;
    }
    
    /**
    * Set the basic authentication login
    *
    * @var $basic_login string
    * @return void
    */
    public function set_basic_login($basic_login)
    {
    	$this->basic_login = $basic_login;
    }
    
    
    /****************************************************************************************/
    
    /**
    * Get the basic authentication password
    *
    * @return string
    */
    public function get_basic_password()
    {
    	return $this->basic_password;
    }
    
    /**
    * Set the basic authentication password
    *
    * @var $basic_password string
    * @return void
    */
    public function set_basic_password($basic_password)
    {
    	$this->basic_password = $basic_password;
    }
    
    
    /****************************************************************************************/
    
    /**
    * Get the HTTP method used to send the request
    *
    * @return string
    */
    public function get_http_method()
    {
    	return $this->http_method;
    }
    
    /**
    * Set the HTTP method used to send the request
    *
    * @var $http_method string
    * @return void
    */
    public function set_http_method($http_method)
    {
        $http_method = strtoupper($http_method);
        
        if($http_method == 'GET' || $http_method == 'POST' || $http_method == 'PUT' || $http_method == 'DELETE' || $http_method == 'HEAD' || $http_method == 'TRACE')
        {
            $this->http_method = strtoupper($http_method);
        }
    }
    
    
    /****************************************************************************************/
    
    /**
    * Get the data to send with the request
    *
    * @return mixed
    */
    public function get_data_to_send()
    {
    	return $this->data_to_send;
    }
    
    /**
    * Set the data to send with the request
    *
    * @var $data_to_send mixed
    * @return void
    */
    public function set_data_to_send($data_to_send)
    {
    	$this->data_to_send = $data_to_send;
    }
    
    
    /****************************************************************************************/
    
    /**
    * Get client certificate path to use. The file may contain the certificate only or the certicate with the key.
    * The certificate must be in PEM format.
    * 
    * @return string
    */
    public function get_client_certificate_file()
    {
    	return $this->client_certificate_file;
    }
    
    /**
    * Set client certificate file to use to authenticate the REST request
    *
    * @var $client_certificate_file string
    * @return void
    */
    public function set_client_certificate_file($client_certificate_file)
    {
    	$this->client_certificate_file = $client_certificate_file;
    }
    
    
    /****************************************************************************************/
    
    /**
    * Get client certificate key file
    *
    * @return string
    */
    public function get_client_certificate_key_file()
    {
    	return $this->client_certificate_key_file;
    }
    
    /**
    * Set client certificate key file to use to authenticate the REST request
    *
    * @var $client_certificate_key_file string
    * @return void
    */
    public function set_client_certificate_key_file($client_certificate_key_file)
    {
    	$this->client_certificate_key_file = $client_certificate_key_file;
    }
    
    
    /****************************************************************************************/
    
    /**
    * Get client certificate key password used for authentication
    *
    * @return string
    */
    public function get_client_certificate_key_password()
    {
    	return $this->client_certificate_key_password;
    }
    
    /**
    * Set client certificate key password used for authentication
    *
    * @var $client_certificate_key_password string
    * @return void
    */
    public function set_client_certificate_key_password($client_certificate_key_password)
    {
    	$this->client_certificate_key_password = $client_certificate_key_password;
    }
    
    
    /****************************************************************************************/
    
	/**
    * Get the checking of the target certificate
    *
    * @return bool
    */
    public function get_check_target_certificate()
    {
    	return $this->check_target_certificate;
    }
    
    /**
    * Set the checking of the target certificate
    *
    * @var $check_target_certificate bool
    * @return void
    */
    public function set_check_target_certificate($check_target_certificate)
    {
    	$this->check_target_certificate = $check_target_certificate;
    }
    
    
    /****************************************************************************************/
    
    /**
    * Get the file containing CA certificates used to verify the target certificate identity
    *
    * @return string
    */
    public function get_target_ca_file()
    {
    	return $this->target_ca_file;
    }
    
    /**
    * Set the file containing CA certificates used to verify the target certificate identity
    *
    * @var $target_ca_file string
    * @return void
    */
    public function set_target_ca_file($target_ca_file)
    {
    	$this->target_ca_file = $target_ca_file;
    }
    
    
    /****************************************************************************************/
    
    /**
     * Send the request to the REST service 
     * 
     * @return RestResult
     */
    public function send_request()
    {
        switch($this->connexion_mode)
        {
            case self :: MODE_CURL:
                return $this->send_curl_request();
                break;
                
            case self :: MODE_PEAR:
                return $this->send_pear_request();
                break;
        }
    }
    
    
    /**
     * Send the request by using the cURL extension
     * 
     * @return RestResult
     */
    private function send_curl_request()
    {
        $result = new RestResult();
        $result->set_request_connexion_mode($this->connexion_mode);
        $result->set_request_http_method($this->http_method);
        $result->set_request_sent_data($this->data_to_send);
        
        $url_info = parse_url($this->url);
        
        if(isset($url_info['port']))
        {
            $url = $url_info['scheme'] . '://' . $url_info['host'] . $url_info['path'];
            
            if(isset($url_info['query']) && strlen($url_info['query']) > 0)
            {
                $url .= '?' . $url_info['query'];
            }
            
            $result->set_request_port($url_info['port']);
            
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_PORT, $url_info['port']);
        }
        else
        {
            $url = $this->url;
            
            $curl = curl_init($url);
        }
        
        $result->set_request_url($url);
        
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->http_method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        if($this->check_target_certificate)
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            
            if(isset($this->target_ca_file))
            {
                curl_setopt($curl, CURLOPT_CAINFO, $this->target_ca_file);
            }
        }
        else
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        }
        
    	/*
         * Client certificate used for authentication
         */
        if(isset($this->client_certificate_file))
        {
            curl_setopt($curl, CURLOPT_SSLCERT, $this->client_certificate_file);
        }
        
        /*
         * Client certificate key used for authentication
         */
        if(isset($this->client_certificate_key_file))
        {
            curl_setopt($curl, CURLOPT_SSLKEY, $this->client_certificate_key_file);
        }
        
    	/*
         * Client certificate key password used for authentication
         */
        if(isset($this->client_certificate_key_password))
        {
            curl_setopt($curl, CURLOPT_SSLKEYPASSWD, $this->client_certificate_key_password);
        }
        
        if(isset($this->basic_login) && isset($this->basic_password))
        {
            curl_setopt($curl, CURLOPT_USERPWD, $this->basic_login . ':' . $this->basic_password);
        }
        
        if(isset($this->data_to_send))
        {
            curl_setopt($curl, CURLOPT_POST, 1);
            
            if(is_string($this->data_to_send))
            {                
                curl_setopt($curl, CURLOPT_POSTFIELDS, $this->data_to_send);
            }
            elseif(is_array($this->data_to_send))
            {
                if(isset($this->data_to_send['content']))
                {
                    /*
                     * If $this->data_to_send is an array and the content to send 
                     * is in $this->data_to_send['content'], we use it
                     */
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $this->data_to_send['content']);
                }
                elseif(isset($this->data_to_send['file']))
                {
                    /*
                     * In case of a file to send, the upload works with an array.
                     * The value of the file must begin with an '@'
                     * e.g:
                     * 		$this->data_to_send['file'] --> array('myDocument.pdf' => '@/path/to/file')
                     */
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $this->data_to_send['file']);
                }
          
                /*
                 * If the mime type is given as a parameter, we use it to set the content-type request
                 */
                if(isset($this->data_to_send['mime']))
                {
                    $headers   = array();
                    $headers[] = 'Content-type: ' . $this->data_to_send['mime'];
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                }
            }
        }
        
        $response_content   = curl_exec($curl);
        $response_http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $response_mime_type = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        $response_error     = curl_error($curl);
        
        $result->set_response_content($response_content);
        $result->set_response_http_code($response_http_code);
        $result->set_response_mime_type($response_mime_type);
        
        if(isset($response_error) && strlen($response_error) > 0)
        {
            $result->set_response_error($response_error);
        }
        elseif($response_http_code < 200 || $response_http_code >= 300)
        {
            $result->set_response_error('The REST request returned an HTTP error code of ' . $response_http_code . ' (' . $this->get_http_code_translation($response_http_code) . ')');
        }
        
        curl_close($curl);
        
        return $result;
    }
    
	
	/**
     * Send the request by using the HTTP_Request class of the PEAR package
     * 
     * @return RestResult
     */
    private function send_pear_request()
    {
        $result = new RestResult();
        $result->set_request_connexion_mode($this->connexion_mode);
        $result->set_request_http_method($this->http_method);
        $result->set_request_sent_data($this->data_to_send);
        $result->set_request_url($this->url);
        
        $request_properties = array();
        $request_properties['method'] = $this->http_method;
        $request_properties['user']   = $this->basic_login;
        $request_properties['pass']   = $this->basic_password;
        
        $request = new HTTP_Request($this->url, $request_properties);
        
        if(isset($this->data_to_send))
        {
            if(is_string($this->data_to_send))
            {
                 $request->setBody($this->data_to_send);
            }
            elseif(is_array($this->data_to_send) && isset($this->data_to_send['content']))
            {
                /*
                 * If $this->data_to_send is an array and the content to send 
                 * is in $this->data_to_send['content'], we use it
                 */
                //$request->addPostData('content', $this->data_to_send['content'], true);
                $request->setBody($this->data_to_send['content']);
            }
            elseif(is_array($this->data_to_send) && isset($this->data_to_send['file']))
            {
                if(is_array($this->data_to_send['file']))
                {
                    $values = array_values($this->data_to_send['file']);
                    if(count($values) > 0)
                    {
                        $file_path = $values[0];

                        if(String :: start_with($file_path, '@'))
                        {
                            $file_path = substr($file_path, 1);
                        }
                        
                        if(file_exists($file_path))
                        {    
                            /*
                             * The file is on the HD, and therefore must be read to be set in the body
                             */
                            $file_content = file_get_contents($file_path);
                        }
                    }
                }
                else
                {
                    /*
                     * Tries to use the file value as the content of a file in memory
                     */
                    $file_content = $this->data_to_send['file'];
                }
                
                $request->setBody($file_content);
            }
            
        	/*
             * If the mime type is given as a parameter, we use it to set the content-type request
             */
            if(is_array($this->data_to_send) && isset($this->data_to_send['mime']))
            {
                $request->addHeader('Content-type', $this->data_to_send['mime']);
            }
        }
        
        $req_result = $request->sendRequest(true);
        if($req_result === true)
        {
            $result->set_response_http_code($request->getResponseCode());
            $result->set_response_content($request->getResponseBody());
        }
        else
        {
            $result->set_response_http_code(curl_getinfo($curl, CURLINFO_HTTP_CODE));
            $result->set_response_error($request->getResponseReason());
        }
        
        return $result;
    }
    
    
    /****************************************************************************************/
    
    public function get_http_code_translation($http_code)
    {
        switch ($http_code)
        {
            case '400':
                return 'Bad Request';
            case '401':
                return 'Unauthorized';
            case '402':
                return 'Payment Required';
            case '403':
                return 'Forbidden';
            case '404':
                return 'Not Found';
            case '405':
                return 'Method Not Allowed';
            case '406':
                return 'Not Acceptable';
            case '407':
                return 'Proxy Authentication Required';
            case '408':
                return 'Request Time-out';
            case '409':
                return 'Conflict';
            case '410':
                return 'Gone';
            case '411':
                return 'Length Required';
            case '412':
                return 'Precondition Failed';
            case '413':
                return 'Request Entity Too Large';
            case '414':
                return 'Request-URI Too Long';
            case '415':
                return 'Unsupported Media Type';
            case '416':
                return 'Requested range unsatisfiable';
            case '417':
                return 'Expectation failed';
            case '422':
                return 'Unprocessable entity';
            case '423':
                return 'Locked';
            case '424':
                return 'Method failure';
                
            case '500':
                return 'Internal Server Error';
            case '501':
                return 'Not Implemented';
            case '502':
                return 'Bad Gateway ou Proxy Error';
            case '503':
                return 'Service Unavailable';
            case '504':
                return 'Gateway Time-out';
            case '505':
                return 'HTTP Version not supported';
            case '507':
                return 'Insufficient storage';
            case '509':
                return 'Bandwidth Limit Exceeded';
                
            default:
                return null;
        }
    }
    
    
}
?>