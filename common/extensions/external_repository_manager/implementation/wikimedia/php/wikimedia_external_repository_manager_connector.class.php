<?php
namespace common\extensions\external_repository_manager\implementation\wikimedia;

use common\libraries;

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

        $login = ExternalSetting :: get('login', $this->get_external_repository_instance_id());
        $password = ExternalSetting :: get('password', $this->get_external_repository_instance_id());

        $parameters['lgname'] = $login;
        $parameters['lgpassword'] = $password;
        $parameters['format'] = 'xml';
        $parameters['redirects'] = true;

        $this->wikimedia->request(WikimediaRestClient :: METHOD_POST, null, $parameters);
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
        if (! $condition)
        {
            $condition = 'Looney Tunes';
        }

        $parameters = array();
        $parameters['action'] = 'query';
        $parameters['generator'] = 'search';
        $parameters['gsrsearch'] = urlencode($condition);
        //define('WIKIMEDIA_FILE_NS', 6);
        $parameters['gsrnamespace'] = 6;
        $parameters['gsrlimit'] = $count;
        $parameters['gsroffset'] = $offset;
        $parameters['prop'] = 'imageinfo';
        $parameters['iiprop'] = 'timestamp|url|dimensions|mime|user|userid|size';
        $parameters['iiurlwidth'] = 192;
        $parameters['iiurlheight'] = 192;
        $parameters['format'] = 'xml';
        $parameters['redirects'] = true;

        $result = $this->wikimedia->request(WikimediaRestClient :: METHOD_GET, null, $parameters);

        $objects = array();

        foreach ($result->get_response_content_xml()->query->pages->page as $page)
        {
            $objects[] = $this->get_image($page);
        }

        return new ArrayResultSet($objects);
    }

    protected function get_image($page)
    {
        $object = new WikimediaExternalRepositoryObject();
        $object->set_id((int) $page->attributes()->pageid);
        $object->set_external_repository_id($this->get_external_repository_instance_id());

        $file_info = pathinfo(substr((string) $page->attributes()->title, 5));
        $object->set_title($file_info['filename']);
        $object->set_description($file_info['filename']);

        $time = strtotime((int) $page->imageinfo->ii->attributes()->timestamp);
        $object->set_created($time);
        $object->set_modified($time);
        $object->set_owner_id((string) $page->imageinfo->ii->attributes()->user);

        $photo_urls = array();

        $original_width = (int) $page->imageinfo->ii->attributes()->width;
        $original_height = (int) $page->imageinfo->ii->attributes()->height;

        if ($original_width <= 192)
        {
            $photo_urls[WikimediaExternalRepositoryObject :: SIZE_THUMBNAIL] = array(
                    'source' => (string) $page->imageinfo->ii->attributes()->url,
                    'width' => $original_width,
                    'height' => $original_height);
        }
        else
        {
            $photo_urls[WikimediaExternalRepositoryObject :: SIZE_THUMBNAIL] = array(
                    'source' => (string) $page->imageinfo->ii->attributes()->thumburl,
                    'width' => (int) $page->imageinfo->ii->attributes()->thumbwidth,
                    'height' => (int) $page->imageinfo->ii->attributes()->thumbheight);
        }

        if ($original_width <= 500)
        {
            $photo_urls[WikimediaExternalRepositoryObject :: SIZE_MEDIUM] = array(
                    'source' => (string) $page->imageinfo->ii->attributes()->url,
                    'width' => $original_width,
                    'height' => $original_height);
        }
        else
        {
            $thumbnail = $this->get_additional_thumbnail_url($page->imageinfo->ii->attributes()->thumburl, 500);
            $thumbnail_dimensions = libraries\ImageManipulation :: rescale($original_width, $original_height, 500, 500);

            $photo_urls[WikimediaExternalRepositoryObject :: SIZE_MEDIUM] = array(
                    'source' => $thumbnail,
                    'width' => $thumbnail_dimensions[0],
                    'height' => $thumbnail_dimensions[1]);
        }

        $photo_urls[WikimediaExternalRepositoryObject :: SIZE_ORIGINAL] = array(
                'source' => (string) $page->imageinfo->ii->attributes()->url,
                'width' => $original_width,
                'height' => $original_height);
        $object->set_urls($photo_urls);

        $object->set_type($file_info['extension']);
        $object->set_rights($this->determine_rights());

        return $object;
    }

    protected function get_additional_thumbnail_url($url, $size)
    {
        $path_info = pathinfo($url);
        $filename = explode('-', $path_info['basename'], 2);
        return $path_info['dirname'] . '/' . $size . 'px-' . $filename[1];
    }

    /**
     * @param mixed $condition
     * @return int
     */
    function count_external_repository_objects($condition)
    {
        if (! $condition)
        {
            $condition = 'Looney Tunes';
        }

        $parameters = array();
        $parameters['action'] = 'query';
        $parameters['generator'] = 'search';
        $parameters['gsrsearch'] = urlencode($condition);
        $parameters['gsrnamespace'] = 6;
        $parameters['gsrlimit'] = 1;
        $parameters['prop'] = 'imageinfo';
        $parameters['iiprop'] = 'timestamp';
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
        $parameters = array();
        $parameters['action'] = 'query';
        $parameters['pageids'] = $id;
        $parameters['gsrnamespace'] = 6;
        $parameters['prop'] = 'imageinfo';
        $parameters['iiprop'] = 'timestamp|url|dimensions|mime|user|userid|size';
        $parameters['iiurlwidth'] = 192;
        $parameters['iiurlheight'] = 192;
        $parameters['format'] = 'xml';
        $parameters['redirects'] = true;

        $result = $this->wikimedia->request(WikimediaRestClient :: METHOD_GET, null, $parameters);
        return $this->get_image($result->get_response_content_xml()->query->pages->page);
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
    function determine_rights()
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