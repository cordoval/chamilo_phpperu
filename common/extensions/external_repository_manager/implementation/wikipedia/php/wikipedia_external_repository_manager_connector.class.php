<?php
namespace common\extensions\external_repository_manager\implementation\wikipedia;

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

require_once dirname(__FILE__) . '/webservices/wikipedia_rest_client.class.php';
require_once dirname(__FILE__) . '/wikipedia_external_repository_object.class.php';

class WikipediaExternalRepositoryManagerConnector extends ExternalRepositoryManagerConnector
{
    /**
     * @var WikipediaRestClient
     */
    private $wikipedia;

    /**
     * @param ExternalRepository $external_repository_instance
     */
    function __construct($external_repository_instance)
    {
        parent :: __construct($external_repository_instance);

        $url = ExternalSetting :: get('url', $this->get_external_repository_instance_id());
        $this->wikipedia = new WikipediaRestClient($url);
        $this->wikipedia->set_connexion_mode(RestClient :: MODE_PEAR);

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

        $this->wikipedia->request(WikipediaRestClient :: METHOD_POST, null, $parameters);
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
        //define('WIKIPEDIA_FILE_NS', 6);
        $parameters['gsrnamespace'] = 0;
        $parameters['gsrlimit'] = $count;
        $parameters['gsroffset'] = $offset;
        $parameters['prop'] = 'info';
        $parameters['inprop'] = 'url';
        $parameters['format'] = 'xml';
        $parameters['export'] = true;
        $parameters['redirects'] = true;

        $result = $this->wikipedia->request(WikipediaRestClient :: METHOD_GET, null, $parameters);

        $objects = array();
        foreach ($result->get_response_content_xml()->query->pages->page as $page)
        {
            $objects[] = $this->get_article($page);
        }
        return new ArrayResultSet($objects);
    }

    protected function get_article($page)
    {
        $object = new WikipediaExternalRepositoryObject();
        $object->set_id((int) $page->attributes()->pageid);
        $object->set_external_repository_id($this->get_external_repository_instance_id());

        $object->set_title((string) $page->attributes()->title);
        $object->set_description((string) $page->attributes()->title);
        $time = strtotime((int) $page->attributes()->touched);
        $object->set_created($time);
        $object->set_modified($time);

        $object->set_urls((string) $page->attributes()->fullurl);

        $object->set_rights($this->determine_rights());

        return $object;
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
        $parameters['gsrnamespace'] = 4;
        $parameters['gsrlimit'] = 1;
        $parameters['prop'] = 'info';
        $parameters['format'] = 'xml';
        $parameters['redirects'] = true;

        $result = $this->wikipedia->request(WikipediaRestClient :: METHOD_GET, null, $parameters);
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
        $parameters['gsrnamespace'] = 4;
        $parameters['prop'] = 'info';
        $parameters['inprop'] = 'url';
        $parameters['format'] = 'xml';
        $parameters['redirects'] = true;

        $result = $this->wikipedia->request(WikipediaRestClient :: METHOD_GET, null, $parameters);
        return $this->get_article($result->get_response_content_xml()->query->pages->page);
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