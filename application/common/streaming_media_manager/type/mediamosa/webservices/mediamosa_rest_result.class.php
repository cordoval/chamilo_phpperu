<?php

/**
 * Description of mediamosa_rest_resultclass
 *
 * @author jevdheyd
 */
class MediamosaRestResult extends RestResult {

    protected $response_header;

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
    function get_response_content()
    {
        if($this->response_content) return new SimpleXMLElement($this->response_content);
    }

}
?>
