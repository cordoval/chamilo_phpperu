<?php

/**
 * Description of mediamosa_rest_clientclass
 *
 * @author jevdheyd
 */

require_once Path::get_plugin_path().'webservices/rest/client/rest_client.class.php';
require_once dirname(__FILE__).'/mediamosa_rest_result.class.php';

class MediamosaRestClient extends RestClient{

    private $mediamosa_url;
    private $header_data;
    private $connector_cookie = null;
    private $proxy;

    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';

    const PARAM_CONNECTOR_COOKIE = 'mediamosa_connector_cookie';
    
    function __construct($mediamosa_url)
    {
        parent::__construct();

        $this->mediamosa_url = $mediamosa_url;

        //check if connector cookie is set
        $cookie = new Cookie();
        if($the_cookie = $cookie->retrieve(self :: PARAM_CONNECTOR_COOKIE))
        {
            $this->set_connector_cookie($the_cookie);
        }
    }

    /*
     * login to mediamosa server
     * a cookie is set with session id
     *
     * @param string username of chamilo for a particular mediamosa server
     * @param string password {idem supra}
     * @return boolean
     */
    function login($username, $password)
    {
       if($username && $password)
       {
           // step 1: request the challenge
            $response = $this->request(self :: METHOD_POST, '/login', array('dbus' => 'AUTH DBUS_COOKIE_SHA1 '. $username));

            if($response->check_result())
            {
                $cookies = $response->get_response_cookies();
                $this->set_connector_cookie($cookies[0]['name'],$cookies[0]['value']);

                //get challenge code
                preg_match('@DATA vpx 0 (.*)@', $response->get_response_content_xml()->items->item->dbus, $matches);
                $challenge = $matches[1];

                //generate something random
                $random = substr(md5(microtime(true)),0,10);

                // step 2: send credentials
                $challenge_response = sha1(sprintf('%s:%s:%s', $challenge, $random, $password));
                $response = $this->request(self :: METHOD_POST, '/login', array('dbus' => sprintf('DATA %s %s', $random, $challenge_response)));

                if($response->check_result())
                {
                    // parse the response
                    preg_match('@(.*)@', $response->get_response_content_xml()->items->item->dbus, $matches);
                    $result = $matches[1];

                    // return TRUE or FALSE
                    return (substr($result, 0, 2) === 'OK');
                }
            }
       }
       return false;
    }

    /*
     * sets login validation cookie
     * @param string cookie
     */
    private function set_connector_cookie($name, $value)
    {
        $this->connector_cookie = array('name' => $name, 'value' => $value);
        $cookie = new Cookie();
        $cookie->register(self :: PARAM_CONNECTOR_COOKIE, $this->connector_cookie); //expire
    }

    /*
     * gets login validation cookie
     * checks if cookie exists and returns
     * @return string or false
     */
    //TODO:jens -> check if cookie exists
    function get_connector_cookie()
    {
        if(!is_null($this->connector_cookie))
        {
            return $this->connector_cookie;
        }
        else
        {
            $cookie = new Cookie();
            $tmp_cookie = $cookie->retrieve(self :: PARAM_CONNECTOR_COOKIE);

            if($tmp_cookie)
            {
                $this->connector_cookie = $tmp_cookie;
                return $this->connector_cookie;
            }
        }

        return false;
    }

    function set_header_data($name,$value)
    {
        $this->header_data[] = array('name' => $name, 'value' => $value);
    }

    function get_header_data()
    {
        return $this->header_data;
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
        if(($method == self :: METHOD_POST) || ($method == self :: METHOD_PUT))
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
        
        $this->set_url($this->mediamosa_url.$url);
        
        //add connector cookie to headers if set
        if($this->get_connector_cookie())
        {
            $connector_cookie = $this->get_connector_cookie();
            $this->set_header_data('Cookie', $connector_cookie['name'].'='.$connector_cookie['value']);
        }
        
        $response = $this->send_request();
        $response->set_response_content_xml();
        return $response;
    }

    /*override of parent function
     *just to add more functionality
     * 1. headers can be set in array key-value pairs
     * 2. headers are returned in array key-value pairs
     */
    protected function send_pear_request()
    {
        
        $result = new MediaMosaRestResult();
        $result->set_request_connexion_mode($this->get_connexion_mode());
        $result->set_request_http_method($this->get_http_method());
        $result->set_request_sent_data($this->get_data_to_send());
        $result->set_request_url($this->get_url());

        $request_properties = array();
        $request_properties['method'] = $this->get_http_method();
        $request_properties['user']   = $this->get_basic_login();
        $request_properties['pass']   = $this->get_basic_password();

        $request = new HTTP_Request($this->get_url(), $request_properties);

        //possibly set a proxy
        if($proxy = $this->get_proxy()) $request->setProxy($proxy['server'], $proxy['port']);

       //add data
        $data_to_send = $this->get_data_to_send();
        
        if(isset($data_to_send))
        {
            //TODO:jens --> redistribute this so it works in all situations
            //problem possible when key = content 
           if(is_string($data_to_send))
            {
                 $request->setBody($data_to_send);
            }
            elseif(is_array($data_to_send) && isset($data_to_send['content']))
            {
                /*
                 * If $this->data_to_send is an array and the content to send
                 * is in $this->data_to_send['content'], we use it
                 */
                //$request->addPostData('content', $this->data_to_send['content'], true);
                $request->setBody($data_to_send['content']);
            }
            elseif(is_array($data_to_send) && isset($data_to_send['file']))
            {
                if(is_array($data_to_send['file']))
                {
                    $values = array_values($data_to_send['file']);
                    if(count($values) > 0)
                    {
                        $file_path = $values[0];

                        if(StringUtilities :: start_with($file_path, '@'))
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
                    $file_content = $data_to_send['file'];
                }

                $request->setBody($file_content);
            }
            else
            {
                foreach($data_to_send as $key => $value)
                {
                    $request->addPostData($key, $value);
                }
            }

        	/*
             * If the mime type is given as a parameter, we use it to set the content-type request
             */
            if(is_array($data_to_send) && isset($data_to_send['mime']))
            {
                $request->addHeader('Content-type', $data_to_send['mime']);
            }

            //TODO: jens --> implement in restclient class*/
            /*
             * OVERRIDE
             */
            /*add additional headers*/
            
            if(is_array($this->get_header_data()))
            {
                foreach($this->get_header_data() as $n => $header)
                {
                    $request->addHeader($header['name'], $header['value']);
                }
            }

        }

        $req_result = $request->sendRequest(true);
        if($req_result === true)
        {
            $result->set_response_http_code($request->getResponseCode());
            $result->set_response_content($request->getResponseBody());
            $result->set_response_header($request->getResponseHeader());
            $result->set_response_cookies($request->getResponseCookies());
        }
        else
        {
            $result->set_response_http_code($request->getResponseCode());
            $result->set_response_error($request->getResponseReason());
        }

        return $result;
    }

    /**
     * Send the request by using the cURL extension
     *
     * @return RestResult
     */
    protected function send_curl_request()
    {
        $result = new MediaMosaRestResult();
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

        $headers = array();

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
                    $this->data_to_send_mimetype = $this->data_to_send['mime'];
                }
            }
        }

        if(isset($this->data_to_send_mimetype))
        {
            $headers[] = 'Content-type: ' . $this->data_to_send_mimetype;
        }

        if(count($headers) > 0)
        {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
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

    function set_proxy($server, $port, $username = null, $password = null)
    {
        $proxy = array();
        $proxy['server'] = $server;
        $proxy['port'] = $port;
        
        if($username)$proxy['username'];
        if($password)$proxy['password'];

        $this->proxy = $proxy;
    }

    function get_proxy()
    {
        return (is_array($this->proxy)) ? $this->proxy : false;
    }
}
?>
