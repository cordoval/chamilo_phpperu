<?php

/**
 * Description of mediamosa_rest_clientclass
 *
 * @author jevdheyd
 */

require_once Path::get_plugin_path().'webservices/rest/client/rest_client.class.php';
require_once dirname(__FILE__).'/photobucket_rest_result.class.php';

class PhotobucketRestClient extends RestClient{

    private $matterhorn_url;
    //private $connector_cookie = null;
    

    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    
    function __construct($photobucket_url)
    {
        parent::__construct();

        $this->photobucket_url = $photobucket_url;
    }

    function array_to_url($data)
    {
        if(is_array($data))
        {
            $tmp = array();

            foreach($data as $key => $value)
            {
                if(is_array($value))
                {
                    $subtmp = array();

                    foreach($value as $subkey => $subvalue)
                    {
                        $tmp[] = $key . '[]' . '=' . $subvalue;
                    }
                }
                else
                {
                    $tmp[] = $key .  '=' . $value;
                }
            }
            return implode('&', $tmp);
        }
    }

    /*
     * a prefab function for a request
     * @param method string
     * @param url string
     * @param data array
     * @return MediaMosaRestResult object
     */
    function request($method, $url, $data = null)
    {
        $this->set_http_method($method);

        $this->set_data_to_send('');

        //different method need different handling of data
        if(($method == self :: METHOD_POST) || ($method == self :: METHOD_PUT) || ($method == self :: METHOD_DELETE)) 
        {
            if(is_array($data)) $this->set_data_to_send($data);
        }
        elseif($method == self :: METHOD_GET)
        {
            if(is_array($data)) 
            {
                $tmp = array();

                foreach($data as $key => $value)
                {
                    if(is_array($value))
                    {
                        $subtmp = array();
                        
                        foreach($value as $subkey => $subvalue)
                        {
                            $tmp[] = $key . '[]' . '=' . $subvalue;
                        }
                    }
                    else
                    {
                        $tmp[] = $key .  '=' . $value;
                    }
                }

                $get_string = implode('&', $tmp);
                $url .= '?' . $get_string;
            }
        }
        
        $this->set_url($this->photobucket_url.$url);
        
        $response = $this->send_request();
        //$response->set_response_content_xml();
        return $response;
    }

    /*override of parent function
     *just to add more functionality
     * 1. headers can be set in array key-value pairs
     * 2. headers are returned in array key-value pairs
     */
//    protected function send_pear_request()
//    {
//        
//        $result = new MediaMosaRestResult();
//        $result->set_request_connexion_mode($this->get_connexion_mode());
//        $result->set_request_http_method($this->get_http_method());
//        $result->set_request_sent_data($this->get_data_to_send());
//        $result->set_request_url($this->get_url());
//
//        $request_properties = array();
//        $request_properties['method'] = $this->get_http_method();
//        $request_properties['user']   = $this->get_basic_login();
//        $request_properties['pass']   = $this->get_basic_password();
//
//        $request = new HTTP_Request($this->get_url(), $request_properties);
//
//        /*
//         * addition
//         */
//        //possibly set a proxy
//        if($proxy = $this->get_proxy()) $request->setProxy($proxy['server'], $proxy['port']);
//
//       //add data
//        $data_to_send = $this->get_data_to_send();
//        
//        if(isset($data_to_send))
//        {
//            
//           if(is_string($data_to_send))
//            {
//                 $request->setBody($data_to_send);
//            }
//            elseif(is_array($data_to_send) && isset($data_to_send['content']))
//            {
//                /*
//                 * If $this->data_to_send is an array and the content to send
//                 * is in $this->data_to_send['content'], we use it
//                 */
//                //$request->addPostData('content', $this->data_to_send['content'], true);
//                $request->setBody($data_to_send['content']);
//            }
//            elseif(is_array($data_to_send) && isset($data_to_send['file']))
//            {
//                if(is_array($data_to_send['file']))
//                {
//                    $values = array_values($data_to_send['file']);
//                    if(count($values) > 0)
//                    {
//                        $file_path = $values[0];
//
//                        if(StringUtilities :: start_with($file_path, '@'))
//                        {
//                            $file_path = substr($file_path, 1);
//                        }
//
//                        if(file_exists($file_path))
//                        {
//                            /*
//                             * The file is on the HD, and therefore must be read to be set in the body
//                             */
//                            $file_content = file_get_contents($file_path);
//                        }
//                    }
//                }
//                else
//                {
//                    /*
//                     * Tries to use the file value as the content of a file in memory
//                     */
//                    $file_content = $data_to_send['file'];
//                }
//
//                $request->setBody($file_content);
//            }
//            /*
//             * addition
//             */
//            elseif(is_array($data_to_send))
//            {
//                foreach($data_to_send as $key => $value)
//                {
//                    $request->addPostData($key, $value);
//                }
//            }
//
//        	/*
//             * If the mime type is given as a parameter, we use it to set the content-type request
//             */
//            if(is_array($data_to_send) && isset($data_to_send['mime']))
//            {
//                $request->addHeader('Content-type', $data_to_send['mime']);
//            }
//
//
//            /*
//             * addition
//             */
//           /*add additional headers*/
//            
//            if(is_array($this->get_header_data()))
//            {
//                foreach($this->get_header_data() as $n => $header)
//                {
//                    $request->addHeader($header['name'], $header['value']);
//                }
//            }
//
//        }
//
//        $req_result = $request->sendRequest(true);
//        if($req_result === true)
//        {
//            $result->set_response_http_code($request->getResponseCode());
//            $result->set_response_content($request->getResponseBody());
//            /*
//             * addition
//             */
//            $result->set_response_header($request->getResponseHeader());
//            $result->set_response_cookies($request->getResponseCookies());
//        }
//        else
//        {
//            $result->set_response_http_code($request->getResponseCode());
//            $result->set_response_error($request->getResponseReason());
//        }
//
//        return $result;
//    }   
}
?>
