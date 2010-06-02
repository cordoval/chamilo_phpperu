<?php

/**
 * Description of mediamosa_rest_resultclass
 *
 * @author jevdheyd
 */
class MediamosaRestResult extends RestResult {

    protected $response_header;
    private $response_cookies;

    function set_response_header($response_header)
    {
        $this->response_header = $response_header;
    }
    
    function get_response_header()
    {
        return $this->response_header;
    }

    /**
     * overrides parent
     * Get the response content and turns it into object
     *
     * @return simplexmlelement object
     */
    function get_response_content_xml()
    {
        if($this->get_response_content())
        {
            return new SimpleXMLElement($this->get_response_content());
        }

    }

    /*
     * sets cookies
     * @param cookies
     */
    function set_response_cookies($response_cookies)
    {
        $this->response_cookies = $response_cookies;
    }

    /*
     * gets reponse_cookies
     * @return cookies
     */
    function get_response_cookies()
    {
        return isset($this->response_cookies) ? $this->response_cookies : false;
    }

}
?>
