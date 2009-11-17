<?php
/**
 * $Id: rest_external_exporter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.export.external_export
 */
require_once Path :: get_repository_path() . '/lib/export/external_export/base_external_exporter.class.php';

abstract class RestExternalExporter extends BaseExternalExporter
{
    /**
     * @var RestClient
     */
    private $rest_client = null;

    /*************************************************************************/
    
    protected function RestExternalExporter($fedora_repository_id = DataClass :: NO_UID)
    {
        parent :: BaseExternalExporter($fedora_repository_id);
    }

    /*************************************************************************/
    
    /**
     * @return RestClient
     */
    protected function get_rest_client()
    {
        if (isset($this->rest_client))
        {
            return $this->rest_client;
        }
        else
        {
            $this->rest_client = new RestClient();
            
            $external_export = $this->get_external_export();
            
            $login = $external_export->get_login();
            $password = $external_export->get_password();
            $client_certificate_file = $external_export->get_client_certificate_file();
            $client_certificate_key_file = $external_export->get_client_certificate_key_file();
            $target_ca_file = $external_export->get_target_ca_file();
            
            if (isset($login) && strlen($login) > 0)
            {
                $this->rest_client->set_basic_login($login);
            }
            
            if (isset($password) && strlen($password) > 0)
            {
                $this->rest_client->set_basic_password($password);
            }
            
            if (isset($client_certificate_file) && strlen($client_certificate_file) > 0)
            {
                $client_certificate_file = Path :: get_repository_path() . 'lib/export/external_export/ssl' . String :: ensure_start_with($client_certificate_file, '/');
                
                $this->rest_client->set_client_certificate_file($client_certificate_file);
            }
            
            if (isset($client_certificate_key_file) && strlen($client_certificate_key_file) > 0)
            {
                $client_certificate_key_file = Path :: get_repository_path() . 'lib/export/external_export/ssl' . String :: ensure_start_with($client_certificate_key_file, '/');
                
                $this->rest_client->set_client_certificate_key_file($client_certificate_key_file);
            }
            
            if (isset($target_ca_file) && strlen($target_ca_file) > 0)
            {
                $target_ca_file = Path :: get_repository_path() . 'lib/export/external_export/ssl' . String :: ensure_start_with($target_ca_file, '/');
                
                $this->rest_client->set_target_ca_file($target_ca_file);
            }
            else
            {
                $this->rest_client->set_check_target_certificate(false);
            }
            
            return $this->rest_client;
        }
    }

    /*************************************************************************/
    
    /**
     * Send a request to a REST service and parse the response as an XML Document
     * @param $url string
     * @param $http_method string
     * @param $request_content string The content to send with the REST request
     * @return DOMDocument or null if the response is not well formed XML
     */
    protected function get_rest_xml_response($url, $http_method, $request_content = null)
    {
        //debug($url);
        

        $rest_client = $this->get_rest_client();
        $rest_client->set_url($url);
        $rest_client->set_http_method($http_method);
        $rest_client->set_data_to_send($request_content);
        $rest_client->set_check_target_certificate(false);
        
        $result = $rest_client->send_request();
        
        $response_content = $result->get_response_content();
        
        if (! $result->has_error() && stripos($response_content, 'Exception') === false)
        {
            /*
    	     * Tries to create a DOMDocument
    	     */
            $document = new DOMDocument();
            
            if (strlen($response_content) > 0 && String :: start_with($response_content, '<?xml'))
            {
                set_error_handler(array($this, 'handle_xml_error'));
                $document->loadXML($response_content);
                restore_error_handler();
            }
            //debug($document);
            

            return $document;
        }
        else
        {
            if (stripos($response_content, 'Exception') === false)
            {
                throw new Exception(htmlentities($result->get_response_error()));
            }
            else
            {
                throw new Exception('<h3>Fedora reponse:</h3><p><strong>URL : </strong>' . $result->get_request_url() . '<p><strong>POST data : </strong>' . htmlentities($result->get_request_sent_data()) . '</p><p><strong>Response : </strong>' . $response_content . '</p>');
            }
        }
    }

    public function handle_xml_error($error_no, $error_str, $error_file, $error_line)
    {
        if ($error_no == E_WARNING && substr_count($error_str, 'DOMDocument') > 0)
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
        if (function_exists('finfo_open'))
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
        
        switch ($extension)
        {
            case 'txt' :
                return 'text';
            case 'xml' :
                return 'text/xml';
            case 'html' :
                return 'text/html';
            
            case 'pdf' :
                return 'application/pdf';
            case 'doc' :
                return 'application/word';
            case 'xls' :
                return 'application/excel';
            case 'ppt' :
            case 'pps' :
                return 'application/powerpoint';
            
            case 'jpg' :
            case 'jpe' :
            case 'jpeg' :
                return 'image/jpeg';
            case 'gif' :
                return 'image/gif';
            case 'png' :
                return 'image/png';
            case 'tiff' :
                return 'image/tiff';
            case 'bmp' :
                return 'image/bmp';
            
            default :
                return null;
        }
    }

}
?>