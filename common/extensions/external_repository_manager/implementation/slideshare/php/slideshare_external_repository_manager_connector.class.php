<?php
namespace common\extensions\external_repository_manager\implementation\slideshare;

use common\libraries;

use common\libraries\Path;
use common\libraries\Request;
use common\libraries\Redirect;
use common\libraries\ActionBarSearchForm;
use common\libraries\ArrayResultSet;
use common\libraries\Session;

use repository\ExternalUserSetting;
use repository\ExternalSetting;
use repository\RepositoryDataManager;

use RestClient;

use common\extensions\external_repository_manager\ExternalRepositoryManagerConnector;
use common\extensions\external_repository_manager\ExternalRepositoryObject;

require_once dirname(__FILE__) . '/slideshare_external_repository_object.class.php';
require_once dirname(__FILE__) . '/webservices/slideshare_rest_client.class.php';

class SlideshareExternalRepositoryManagerConnector extends ExternalRepositoryManagerConnector
{
       
    private $slideshare;
    private $consumer_key;
    private $consumer_secret;
    private $user;
	private $password;
    /**
     * @param ExternalRepository $external_repository_instance
     */
    function __construct($external_repository_instance)
    {
        parent :: __construct($external_repository_instance);
        
        $this->consumer_key = ExternalSetting :: get('consumer_key', $this->get_external_repository_instance_id());
        $this->consumer_secret = ExternalSetting :: get('consumer_secret', $this->get_external_repository_instance_id());
        $this->user = ExternalSetting :: get('username', $this->get_external_repository_instance_id());
        $this->password = ExternalSetting :: get('password', $this->get_external_repository_instance_id());
        $this->slideshare = new SlideshareRestClient('https://www.slideshare.net/api/2/');
        $this->slideshare->set_connexion_mode(RestClient :: MODE_PEAR);
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
        if(is_null($condition ))
        {
        	$condition = 'default';
        }        
        $date = time();
        $hash = sha1($this->consumer_secret . $date);
    	$params = array();
    	$params['api_key'] = $this->consumer_key;
    	$params['ts'] = $date;   	
    	$params['hash'] = $hash;
    	$params['tag'] = $condition;
    	$params['limit'] = $count;
    	$params['offset'] = $offset;
                
        $result = $this->slideshare->request(SlideshareRestClient :: METHOD_GET, 'get_slideshows_by_tag', $params);
        $slideshows = (array) $result->get_response_content_xml();
        
		$objects = array();
        foreach ($slideshows['Slideshow'] as $slideshow)
        {
            $objects[] = $this->get_slideshow($slideshow);
        }        
        return new ArrayResultSet($objects);
    }
    
    function get_slideshow($slideshow)
    {
    	$slideshow = (array) $slideshow;
    	$object = new SlideshareExternalRepositoryObject();
        $object->set_id((int) $slideshow['ID']);
        $object->set_external_repository_id($this->get_external_repository_instance_id());

        $object->set_title((string) $slideshow['Title']);
        $object->set_description((string) $slideshow['Description']);
        $object->set_created($slideshow['Created']);
        $object->set_modified($slideshow['Updated']);

        $object->set_urls((string) $slideshow['URL']);
        $object->set_thumbnail($slideshow['ThumbnailSmallURL']);

        $object->set_rights($this->determine_rights());
		return $object;
    }

    /**
     * @param mixed $condition
     * @return int
     */
    function count_external_repository_objects($condition)
    {
        if(is_null($condition ))
        {
        	$condition = 'default';
        }        
        $date = time();
        $hash = sha1($this->consumer_secret . $date);
    	$params = array();
    	$params['api_key'] = $this->consumer_key;
    	$params['ts'] = $date;   	
    	$params['hash'] = $hash;
    	$params['tag'] = $condition;
                
        $result = $this->slideshare->request(SlideshareRestClient :: METHOD_GET, 'get_slideshows_by_tag', $params);
        $slideshows = (array) $result->get_response_content_xml();
        
		$objects = array();
		$count = 0;
        foreach ($slideshows['Slideshow'] as $slideshow)
        {
            $count++;
        }        
        return $count;
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
        if (count($order_properties) > 0)
        {
            $order_property = $order_properties[0]->get_property();
            if ($order_property == self :: SORT_RELEVANCE)
            {
                return $order_property;
            }
            else
            {
                $sorting_direction = $order_properties[0]->get_direction();
                
                if ($sorting_direction == SORT_ASC)
                {
                    return $order_property . '-asc';
                }
                elseif ($sorting_direction == SORT_DESC)
                {
                    return $order_property . '-desc';
                }
            }
        }
        
        return null;
    }

    /**
     * @return array
     */
    static function get_sort_properties()
    {
        $feed_type = Request :: get(SlideshareExternalRepositoryManager :: PARAM_FEED_TYPE);
        $query = ActionBarSearchForm :: get_query();
        
        return array();    
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManagerConnector#retrieve_external_repository_object()
     */
    function retrieve_external_repository_object($id)
    {
        $date = time();
        $hash = sha1($this->consumer_secret . $date);
    	$params = array();
    	$params['slideshow_id'] = $id;    	
    	$params['api_key'] = $this->consumer_key;
    	$params['ts'] = $date;   	
    	$params['hash'] = $hash;
    	$slideshow = $this->slideshare->request(SlideshareRestClient :: METHOD_GET, 'get_slideshow', $params);
       	$slideshow = (array) $slideshow->get_response_content_xml();
    	
       	$object = new SlideshareExternalRepositoryObject();
        $object->set_external_repository_id($this->get_external_repository_instance_id());
        $object->set_id($slideshow['ID']);
        $object->set_title($slideshow['Title']);
        $object->set_description($slideshow['Description']);
        $object->set_created($slideshow['Created']);
        $object->set_modified($slideshow['Updated']);
        $object->set_owner_id($slideshow['Username']);
        $object->set_urls($slideshow['URL']);
        $object->set_embed($slideshow['Embed']);
        $object->set_rights($this->determine_rights());
        
        return $object;    
    }

    /**
     * @param array $values
     * @return boolean
     */
    function update_external_repository_object($values)
    {
        /*$date = time();
        $hash = sha1($this->consumer_secret . $date);
    	$params = array();
    	$params['api_key'] = $this->consumer_key;
    	$params['ts'] = $date;   	
    	$params['hash'] = $hash;
    	$params['slideshow_id'] = $values[SlideshareExternalRepositoryObject :: PROPERTY_ID];
                
        $result = $this->slideshare->request(SlideshareRestClient :: METHOD_GET, 'edit_slideshow', $params);
        $slideshows = (array) $result->get_response_content_xml();
        */
    }

    /**
     * @param array $values
     * @return mixed
     */
    function create_external_repository_object($values, $slideshow)
    {
       	$date = time();
        $hash = sha1($this->consumer_secret . $date);
    	$params = array();
    	$params['api_key'] = $this->consumer_key;
    	$params['ts'] = $date;   	
    	$params['hash'] = $hash;    	   	  	
    	$params['username'] = $this->user;
    	$params['password'] = $this->password;
    	$params['slideshow_title'] = $values['title'];    	
    	$params['slideshow_srcfile'] = file_get_contents($slideshow['tmp_name']);   	
        $this->slideshare->set_header_data('Content-Type', 'multipart/form-data');
        $this->slideshare->set_header_data('enctype', 'multipart/form-data');
        $slideshow1 = $this->slideshare->request(SlideshareRestClient :: METHOD_POST, 'upload_slideshow', $params);
        /*$slideshow1 = $slideshow1->get_response_content_xml();
       	*/
    }

    /**
     * @param ContentObject $content_object
     * @return mixed
     */
    function export_external_repository_object($content_object)
    {
           
    }

    /**
     * @param int $license
     * @return boolean
     */
    function determine_rights()
    {
        $rights = array();
        $rights[ExternalRepositoryObject :: RIGHT_USE] = true;
        $rights[ExternalRepositoryObject :: RIGHT_EDIT] = true;
        $rights[ExternalRepositoryObject :: RIGHT_DELETE] = false;
        $rights[ExternalRepositoryObject :: RIGHT_DOWNLOAD] = false;
        return $rights;
    }

    /**
     * @param string $id
     * @return mixed
     */
    function delete_external_repository_object($id)
    {
        /*$date = time();
        $hash = sha1($this->consumer_secret . $date);
    	$params = array();
    	$params['api_key'] = $this->consumer_key;
    	$params['ts'] = $date;   	
    	$params['hash'] = $hash;
    	$params['slideshow_id'] = $id;  
    	$params['username'] = $this->user;
    	$params['password'] = $this->password; 	
    	
    	$slideshow = $this->slideshare->request(SlideshareRestClient :: METHOD_GET, 'delete_slideshow', $params);
       	$slideshow = (array) $slideshow->get_response_content_xml();
       	*/
    }
    
	function download_external_repository_object($id)
    {
    	
    }
}
?>