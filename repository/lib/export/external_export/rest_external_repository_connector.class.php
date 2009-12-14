<?php

abstract class RestExternalRepositoryConnector extends BaseExternalRepositoryConnector 
{
    /**
     * @var RestClient
     */
    private $rest_client = null;
    
    /*************************************************************************/
    
	protected function RestExternalRepositoryConnector($fedora_repository_id = DataClass :: NO_UID) 
	{
		parent :: BaseExternalRepositoryConnector($fedora_repository_id);
	}
	
	
	/*************************************************************************/
	
	/**
	 * @return RestClient
	 */
	protected function get_rest_client()
	{
	    if(isset($this->rest_client))
	    {
	        return $this->rest_client;
	    }
	    else
	    {
	        $this->rest_client = new RestClient();
	        
	        $external_repository = $this->get_external_repository();
	        
	        $login                       = $external_repository->get_login();
	        $password                    = $external_repository->get_password();
	        $client_certificate_file     = $external_repository->get_client_certificate_file();
	        $client_certificate_key_file = $external_repository->get_client_certificate_key_file();
	        $target_ca_file              = $external_repository->get_target_ca_file();
	        
	        if(isset($login) && strlen($login) > 0)
	        {
	            $this->rest_client->set_basic_login($login);
	        }
	        
	        if(isset($password) && strlen($password) > 0)
	        {
	            $this->rest_client->set_basic_password($password);
	        }
	        
	        if(isset($client_certificate_file) && strlen($client_certificate_file) > 0)
	        {
	            $client_certificate_file = Path :: get_repository_path() . 'lib/export/external_export/ssl' . StringUtilities :: ensure_start_with($client_certificate_file, '/');
	            
	            $this->rest_client->set_client_certificate_file($client_certificate_file);
	        }
	        
	        if(isset($client_certificate_key_file) && strlen($client_certificate_key_file) > 0)
	        {
	            $client_certificate_key_file = Path :: get_repository_path() . 'lib/export/external_export/ssl' . StringUtilities :: ensure_start_with($client_certificate_key_file, '/');
	            
	            $this->rest_client->set_client_certificate_key_file($client_certificate_key_file);
	        }
	        
	        if(isset($target_ca_file) && strlen($target_ca_file) > 0)
	        {
	            $target_ca_file = Path :: get_repository_path() . 'lib/export/external_export/ssl' . StringUtilities :: ensure_start_with($target_ca_file, '/');
	            
	            $this->rest_client->set_target_ca_file($target_ca_file);
	        }
	        else
	        {
	            $this->rest_client->set_check_target_certificate(false);
	        }
	        
	        //$this->rest_client->set_connexion_mode(RestClient :: MODE_PEAR);
	        
	        return $this->rest_client;
	    }
	}
	
	
	/*************************************************************************/
    
	/**
	 * Send a request to a REST service and parse the response as an XML Document
	 * @param $url string
	 * @param $http_method string
	 * @param $data_to_send string The content to send with the REST request
	 * @param $content_mimetype The mimetype of the content to send with the REST request
	 * @return DOMDocument or null if the response is not well formed XML
	 */
	protected function get_rest_xml_response($url, $http_method, $data_to_send = null, $content_mimetype = null)
	{
	    //debug($url);
	    
	    $rest_client = $this->get_rest_client();
	    $rest_client->set_url($url);
	    $rest_client->set_http_method($http_method);
	    
	    if(isset($data_to_send))
	    {
	        if(file_exists($data_to_send) && !isset($content_mimetype))
	        {
	            $content_mimetype = $this->get_file_mimetype($data_to_send);
	        }
	        
	        $rest_client->set_data_to_send($data_to_send, $content_mimetype);
	    }
	    
	    $rest_client->set_check_target_certificate(false);
	    
	    $result = $rest_client->send_request();
	    
	    $response_content = $result->get_response_content();
	    
	    if(!$result->has_error() && stripos($response_content, 'Exception') === false)
	    {
            /*
    	     * Tries to create a DOMDocument
    	     */
    	    $document = new DOMDocument();
        
    	    //if(strlen($response_content) > 0 && StringUtilities :: start_with($response_content, '<?xml'))
    	    if(StringUtilities :: has_value($response_content))
    	    { 
        	    set_error_handler(array($this, 'handle_xml_error'));
        	    $document->loadXML($response_content);
        	    restore_error_handler();
    	    }
    	    
    	    return $document;
	    }
	    else
	    {
	        if(stripos($response_content, 'Exception') === false)
	        {
	            throw new Exception(htmlentities($result->get_response_error()));
	        }
	        else
	        {
	            throw new Exception('<h3>REST response:</h3><p><strong>URL : </strong>' . $result->get_request_url() . '<p><strong>POST data : </strong>' . htmlentities($result->get_request_sent_data()) . '</p><p><strong>Response : </strong>' . $response_content . '</p>');
	        }
	    }
	} 
	
	/**
	 * Send a request to a REST service and return the response
	 * 
	 * @param $url string
	 * @param $http_method string
	 * @param $data_to_send string The content to send with the REST request
	 * @param $content_mimetype The mimetype of the content to send with the REST request
	 * @return mixed
	 */
	protected function get_rest_response($url, $http_method, $data_to_send = null, $content_mimetype = null)
	{
	    //debug($url);
	    
	    $rest_client = $this->get_rest_client();
	    $rest_client->set_url($url);
	    $rest_client->set_http_method($http_method);
	    
	    if(isset($data_to_send))
	    {
	        if(file_exists($data_to_send) && !isset($content_mimetype))
	        {
	            $content_mimetype = $this->get_file_mimetype($data_to_send);
	        }
	        
	        $rest_client->set_data_to_send($data_to_send, $content_mimetype);
	    }
	    
	    $rest_client->set_check_target_certificate(false);
	    
	    $result = $rest_client->send_request();
	    
	    $response_content = $result->get_response_content();
	    
	    if(!$result->has_error() && stripos($response_content, 'Exception') === false)
	    {
    	    return $response_content;
	    }
	    else
	    {
	        if(stripos($response_content, 'Exception') === false)
	        {
	            throw new Exception(htmlentities($result->get_response_error()));
	        }
	        else
	        {
	            throw new Exception('<h3>REST response:</h3><p><strong>URL : </strong>' . $result->get_request_url() . '<p><strong>POST data : </strong>' . htmlentities($result->get_request_sent_data()) . '</p><p><strong>Response : </strong>' . $response_content . '</p>');
	        }
	    }
	} 
	
	public function handle_xml_error($error_no, $error_str, $error_file, $error_line)
	{
	    if ($error_no == E_WARNING && substr_count($error_str,'DOMDocument') > 0)
        {
            throw new DOMException($error_str);
        }
        else
        {
            return false;
        }
	}
	
	
	/*************************************************************************/
	
	protected function get_file_mimetype($path_to_file)
	{
	    if(function_exists('finfo_open'))
	    {
	        /*
	         * PHP >= 5.3 or PECL fileinfo installed
	         */
	        $handle = finfo_open(FILEINFO_MIME);
	        return finfo_file($handle, $file);
	    }
	    else
	    {
	        $path_info = pathinfo($path_to_file);
	        return $this->get_mimetype_from_extension($path_info['extension']);
	    }
	}
	
	protected function get_mimetype_from_extension($extension)
	{
	    $extension = strtolower($extension);
	    
	    switch($extension)
        {
            case 'txt':
                return 'text';
            case 'xml':
                return 'text/xml';
            case 'html':
                return 'text/html';
                
            case 'pdf':
                return 'application/pdf';
            case 'doc':
                return 'application/word';
            case 'xls':
                return 'application/excel';
            case 'ppt':
            case 'pps':
                return 'application/powerpoint';
                
            case 'jpg':
            case 'jpe':
            case 'jpeg':
                return 'image/jpeg';
            case 'gif':
                return 'image/gif';
            case 'png':
                return 'image/png';
            case 'tiff':
                return 'image/tiff';
            case 'bmp':
                return 'image/bmp';
            
            default:
                return null;
        }
	}
    
    
}
?>