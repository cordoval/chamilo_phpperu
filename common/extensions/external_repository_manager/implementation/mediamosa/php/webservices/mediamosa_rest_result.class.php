<?php
namespace common\extensions\external_repository_manager\implementation\mediamosa;
use common\libraries\Path;
use RestResult;
use SimpleXMLElement;

//require_once dirname(__FILE__) . '/../../../../../../../common/libraries/plugin/webservices/rest/client/rest_result.class.php';
require_once Path :: get_plugin_path().'/webservices/rest/client/rest_result.class.php';
/**
 * Description of mediamosa_rest_resultclass
 *
 * @author jevdheyd
 */
class MediamosaRestResult extends RestResult {

//    private $response_content_xml;

//    function set_response_content_xml()
//    {
//        if($this->get_response_content())
//        {
//            if($xml = simplexml_load_string($this->get_response_content()))
//            {
//                $this->response_content_xml = new SimpleXMLElement($this->get_response_content());
//            }
//        }
//    }
//
//    /**
//     * overrides parent
//     * Get the response content and turns it into object
//     *
//     * @return simplexmlelement object
//     */
//    function get_response_content_xml()
//    {
//        return isset($this->response_content_xml) ? $this->response_content_xml : false;
//
//    }



    //verifies if request has succeeded
    function check_result($error = false, $ok = false)
    {
        $result_id = (int)$this->get_response_content_xml()->header->request_result_id;

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
