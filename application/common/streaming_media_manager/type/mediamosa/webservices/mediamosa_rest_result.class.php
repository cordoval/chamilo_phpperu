<?php

/**
 * Description of mediamosa_rest_resultclass
 *
 * @author jevdheyd
 */
class MediamosaRestResult extends RestResult {

    protected $response_header;
    private $response_cookies;
    private $response_content_xml;

    function set_response_header($response_header)
    {
        $this->response_header = $response_header;
    }
    
    function get_response_header()
    {
        return $this->response_header;
    }
    
    function set_response_content_xml()
    {
        if($this->get_response_content())
        {
            $this->response_content_xml = new SimpleXMLElement($this->get_response_content());
        }
    }
    
    /**
     * overrides parent
     * Get the response content and turns it into object
     *
     * @return simplexmlelement object
     */
    function get_response_content_xml()
    {
        return isset($this->response_content_xml) ? $this->response_content_xml : false;

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

    //verifies if request has succeeded
    function check_result($error = false, $ok = false)
    {
        $result_id = (int)$this->response_content_xml->header->request_result_id;

        if ($result_id != 601 && $result_id != 705)
        {
          if ($error)
          {
            //TODO: output error
          }
          return false;
        }
        else
        {
          if ($ok)
          {
            //TODO:output succeeded
          }
          return true;
        }
    }
}
?>
