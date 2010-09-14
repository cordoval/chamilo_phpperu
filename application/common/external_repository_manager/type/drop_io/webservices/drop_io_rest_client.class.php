<?php

/**
 * Description of mediamosa_rest_clientclass
 *
 * @author jevdheyd
 */

require_once Path::get_plugin_path().'webservices/rest/client/rest_client.class.php';
require_once dirname(__FILE__).'/drop_io_rest_result.class.php';

class DropIoRestClient extends RestClient{

    private $drop_io_url;   

    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    
    function __construct($drop_io_url)
    {
        parent::__construct();

        $this->drop_io_url = $drop_io_url;
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
        
        $this->set_url($this->matterhorn_url.$url);
                
        $response = $this->send_request();
        return $response;
    }   
}
?>
