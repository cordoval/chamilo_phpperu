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

    function login($username, $password)
    {
        $username = $username;
        $password = $password;
        
        // step 1: request the challenge
        $response = $this->request('POST', '/login', array('dbus' => 'AUTH DBUS_COOKIE_SHA1 '. $username));

        // parse response
        $header = $response->get_response_header();
        $this->set_connector_cookie(explode($header['Set-cookie']));

        //get challenge code
        preg_match('@<dbus>DATA vpx 0 (.*)</dbus>@', $response->get_response_content(), $matches);
        $challenge = $matches[1];

        // step 2: send credentials
        $challenge_response = sha1(sprintf('%s:%s:%s', $challenge, $random, $password));
        $response = $this->request('POST', '/login', array('dbus' => sprintf('DATA %s %s', $random, $challenge_response)));

        // parse the response
        preg_match('@<dbus>(.*)</dbus>@', $response->data, $matches);
        $result = $matches[1];

        // return TRUE or FALSE
        return (substr($result, 0, 2) === 'OK');
    }

    /*
     * sets login validation cookie
     * @param string cookie
     */
    private function set_connector_cookie($cookie)
    {
        $this->connector_cookie = $cookie;
        $cookie = new Cookie();
        $cookie->register(self :: PARAM_CONNECTOR_COOKIE, $this->connector_cookie); //expire
    }

    /*
     * gets login validation cookie
     * checks if cookie exists and returns
     * @return string or false
     */
    function get_connector_cookie()
    {
        return !is_null($this->connector_cookie) ?  $this->connector_cookie : false;
    }

    /*
     * @param array data
     * @return string
     */
    function array_to_xml($data){
        if(is_array($data)){

            foreach($data as $key=>$value)
            {
                $output = '<'.$key.'>'.$value.'</'.$key.'>';
            }

            return $output;
        }
        return null;
    }

    function set_header_data($data = array())
    {
        $this->header_data = $data;
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
    function request($method, $url, $data)
    {
        $this->set_http_method($method);
        $this->set_url($this->mediamosa_url, $url);
        if(is_array($data)) $this->set_data_to_send($this->array_to_xml($data));
        return $this->send_request();
    }

    /*override of parent function
     *just to add more functionality
     * 1. headers can be set in array key-value pairs
     * 2. headers are returned in array key-value pairs
     */
    protected function send_pear_request()
    {
        $result = new MediaMosaRestResult();
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

            //TODO: jens --> implement in restclient class*/
            /*
             * OVERRIDE
             */
            /*add additional headers*/
            if(is_array($this->get_header_data()))
            {
                foreach($this->header_data as $name => $value)
                {
                    $request->addHeader($name, $value);
                }
            }

        }

        $req_result = $request->sendRequest(true);
        if($req_result === true)
        {
            $result->set_response_http_code($request->getResponseCode());
            $result->set_response_content($request->getResponseBody());
            $result->set_response_header($request->getResponseHeader());
        }
        else
        {
            $result->set_response_http_code(curl_getinfo($curl, CURLINFO_HTTP_CODE));
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
}
?>
