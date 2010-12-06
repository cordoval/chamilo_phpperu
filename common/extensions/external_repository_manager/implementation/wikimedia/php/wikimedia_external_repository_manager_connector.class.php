<?php
namespace common\extensions\external_repository_manager\implementation\wikimedia;

use common\libraries\Path;
use common\libraries\Request;
use common\libraries\Redirect;
use common\libraries\ActionBarSearchForm;
use common\libraries\ArrayResultSet;
use common\libraries\Session;

use RestClient;

use repository\ExternalUserSetting;
use repository\ExternalSetting;
use repository\RepositoryDataManager;

use common\extensions\external_repository_manager\ExternalRepositoryManagerConnector;
use common\extensions\external_repository_manager\ExternalRepositoryObject;

require_once dirname(__FILE__) . '/webservices/wikimedia_rest_client.class.php';
require_once dirname(__FILE__) . '/wikimedia_external_repository_object.class.php';

/**
 * @author Scaramanga
 *
 * Test developer key for Wikimedia: 61a0f40b9cb4c22ec6282e85ce2ae768
 * Test developer secret for Wikimedia: e267cbf5b7a1ad23
 */

class WikimediaExternalRepositoryManagerConnector extends ExternalRepositoryManagerConnector
{
    /**
     * @var WikimediaRestClient
     */
    private $wikimedia;

    /**
     * @param ExternalRepository $external_repository_instance
     */
    function __construct($external_repository_instance)
    {
        parent :: __construct($external_repository_instance);

        $url = ExternalSetting :: get('url', $this->get_external_repository_instance_id());
        $this->wikimedia = new WikimediaRestClient($url);
        $this->wikimedia->set_connexion_mode(RestClient :: MODE_PEAR);

        $this->login();
    }

    function login()
    {
        $parameters = array();
        $parameters['action'] = 'login';
        $parameters['lgname'] = 'chamilo';
        $parameters['lgpassword'] = 'ch4m1l0';
        $parameters['format'] = 'xml';
        $parameters['redirects'] = true;

        $result = $this->wikimedia->request(WikimediaRestClient :: METHOD_POST, null, $parameters);
        //        var_dump($result);


    //        $content = $this->_conn->post($this->api, $this->_param);
    //        $result = unserialize($content);
    //        if (! empty($result['result']['sessionid']))
    //        {
    //            $this->userid = $result['result']['lguserid'];
    //            $this->username = $result['result']['lgusername'];
    //            $this->token = $result['result']['lgtoken'];
    //            return true;
    //        }
    //        else
    //        {
    //            return false;
    //        }
    }

    /**
     * @param int $instance_id
     * @return WikimediaExternalRepositoryManagerConnector:
     */
    static function get_instance($instance_id)
    {
        if (! isset(self :: $instance[$instance_id]))
        {
            self :: $instance[$instance_id] = new WikimediaExternalRepositoryManagerConnector($instance_id);
        }
        return self :: $instance[$instance_id];
    }

    /**
     * @param mixed $condition
     * @param ObjectTableOrder $order_property
     * @param int $offset
     * @param int $count
     * @return ArrayResultSet
     */
    function retrieve_external_repository_objects($condition = null, $order_property, $offset, $count)
    {
        //define('WIKIMEDIA_FILE_NS', 6);
        //define('WIKIMEDIA_IMAGE_SIDE_LENGTH', 1024);

        $parameters = array();
        $parameters['action'] = 'query';
        $parameters['generator'] = 'search';
        $parameters['gsrsearch'] = 'Disney';
        $parameters['gsrnamespace'] = 6;
        $parameters['gsrlimit'] = $count;
        $parameters['prop'] = 'imageinfo';
        $parameters['iiprop'] = 'url|dimensions|mime|user|userid|size';
        $parameters['iiurlwidth'] = 1024;
        $parameters['iiurlheight'] = 1024;
        $parameters['format'] = 'xml';
        $parameters['redirects'] = true;

        $result = $this->wikimedia->request(WikimediaRestClient :: METHOD_GET, null, $parameters);

        foreach($result->get_response_content_xml()->query->pages->page as $page)
        {
            dump($page);
            exit;
        }

        return new ArrayResultSet(array());
    }

    /**
     * @param mixed $condition
     * @return int
     */
    function count_external_repository_objects($condition)
    {
        $parameters = array();
        $parameters['action'] = 'query';
        $parameters['generator'] = 'search';
        $parameters['gsrsearch'] = 'Disney';
        $parameters['gsrnamespace'] = 6;
        $parameters['gsrlimit'] = 1;
        $parameters['prop'] = 'imageinfo';
        $parameters['iiprop'] = 'timestamp';
        $parameters['iiurlwidth'] = 1024;
        $parameters['iiurlheight'] = 1024;
        $parameters['format'] = 'xml';
        $parameters['redirects'] = true;

        $result = $this->wikimedia->request(WikimediaRestClient :: METHOD_GET, null, $parameters);
        return $result->get_response_content_xml()->query->searchinfo->attributes()->totalhits;
    }

    /**
     * @param string $query
     * @return string
     */
    static function translate_search_query($query)
    {
        return $query;
    }

    /**
     * @param ObjectTableOrder $order_properties
     * @return string|null
     */
    function convert_order_property($order_properties)
    {
        return null;
    }

    /**
     * @return array
     */
    static function get_sort_properties()
    {
        return array();

    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManagerConnector#retrieve_external_repository_object()
     */
    function retrieve_external_repository_object($id)
    {
        $object = new WikimediaExternalRepositoryObject();
        return $object;
    }

    /**
     * @param array $values
     * @return boolean
     */
    function update_external_repository_object($values)
    {
        return true;
    }

    function export_external_repository_object($id)
    {
        return true;
    }

    /**
     * @param int $license
     * @param string $photo_user_id
     * @return boolean
     */
    function determine_rights($license, $photo_user_id)
    {
        $rights = array();
        $rights[ExternalRepositoryObject :: RIGHT_USE] = true;
        $rights[ExternalRepositoryObject :: RIGHT_EDIT] = false;
        $rights[ExternalRepositoryObject :: RIGHT_DELETE] = false;
        $rights[ExternalRepositoryObject :: RIGHT_DOWNLOAD] = true;

        return $rights;
    }

    /**
     * @param string $id
     * @return mixed
     */
    function delete_external_repository_object($id)
    {
        return true;
    }
}
?>