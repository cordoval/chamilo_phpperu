<?php
namespace common\extensions\external_repository_manager\implementation\box;

use common\libraries\Redirect;
use common\libraries\Request;
use common\libraries\Path;
use common\libraries\Session;
use common\libraries\Utilities;
use common\libraries\ArrayResultSet;
use common\libraries\ActionBarSearchForm;
use common\libraries\Filesystem;

use common\extensions\external_repository_manager\ExternalRepositoryConnector;
use common\extensions\external_repository_manager\ExternalRepositoryObject;

use repository\RepositoryDataManager;
use repository\ExternalRepositorySetting;
use repository\ExternalRepositoryUserSetting;

use boxclient;

require_once Path :: get_plugin_path(__NAMESPACE__) . 'box-api/boxlibphp5.php';
require_once dirname(__FILE__) . '/box_external_repository_object.class.php';

class BoxExternalRepositoryConnector extends ExternalRepositoryConnector
{
    private $boxnet;
    private $key;
	private $ticket;	
	
    function __construct($external_repository_instance)
    {
        parent :: __construct($external_repository_instance);
        $this->key = ExternalRepositorySetting :: get('key', $this->get_external_repository_instance_id());
        $session_token = ExternalRepositoryUserSetting :: get('session_token', $this->get_external_repository_instance_id());
        $this->boxnet = new boxclient($this->key, '');
        
        $ticket_return = $this->boxnet->getTicket();

		$this->ticket = $ticket_return['ticket'];
		
		if ($this->ticket && ($auth_token == '') && $_REQUEST['auth_token']) 
    	{
    		$auth_token = $_REQUEST['auth_token'];
    	}
    	elseif ($this->ticket && ($auth_token == '') && is_null($session_token)) 
    	{
    		$this->boxnet->getAuthToken($this->ticket);    		
		}
		if(is_null($session_token) && !is_null($auth_token))
		{
			$setting = RepositoryDataManager :: get_instance()->retrieve_external_repository_setting_from_variable_name('session_token', $this->get_external_repository_instance_id());
		    $user_setting = new ExternalRepositoryUserSetting();
        	$user_setting->set_setting_id($setting->get_id());
        	$user_setting->set_user_id(Session :: get_user_id());
        	$user_setting->set_value($auth_token);
        	$user_setting->create();
		}
		$session_token = ExternalRepositoryUserSetting :: get('session_token', $this->get_external_repository_instance_id());
		
        $this->boxnet = new boxclient($this->boxnet->api_key, $session_token);			     
	}      
	
    /**
     * @param mixed $condition
     * @param ObjectTableOrder $order_property
     * @param int $offset
     * @param int $count
     * @return array
     */
    function retrieve_files($condition = null, $order_property, $offset, $count)
    {    	
        $folder = Request::get('folder');
        if(is_null($folder))
        	$folder = 0;       	    	
       	$files = $this->boxnet->get_files($folder);
       	return $files;
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
        $tree = $this->retrieve_files($order_property, $offset, $count);
    	$folders = array();
    	foreach($tree as $fold)
    	{
    		if($fold['file_name'] != '')
    		{    			
    			$object = new BoxExternalRepositoryObject();
    			$object->set_id($fold['file_id']);
            	$object->set_external_repository_id($this->get_external_repository_instance_id());
            	$object->set_title($fold['file_name']);            	
            	$object->set_created(date("m.d.y", $fold['created']));
            	$object->set_modified(date("m.d.y", $fold['updated']));
            	$object->set_description($fold['description']);
            	$object->set_rights($this->determine_rights());
            	$objects[] = $object;
    		}            
    	}
    	return new ArrayResultSet($objects);
    }
    
    function retrieve_folders($folder_url)
    {
    	$tree = $this->boxnet->getAccountTree();
    	$folders = array();
    	
    	foreach($tree as $fold)
    	{
    		if($fold['folder_name'] != '')
    		{
    			$folder[] = array();    		
    			$folder['title'] = $fold['folder_name'];
            	$folder['url'] = str_replace('__PLACEHOLDER__', $fold['folder_id'], $folder_url);
            	$folder['class'] = 'category';
            	$folders[] = $folder; 
    		}            
    	}
    	return $folders;
    }	

    /**
     * @param mixed $condition
     * @return int
     */
    function count_external_repository_objects($condition)
    {
        $files = $this->retrieve_files($condition, $order_property, 1, 1);
        return $files['total'];
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
        $feed_type = Request :: get(BoxExternalRepositoryManager :: PARAM_FEED_TYPE);
        $query = ActionBarSearchForm :: get_query();

        if (($feed_type == BoxExternalRepositoryManager :: FEED_TYPE_GENERAL && $query))
        {
            return array(self :: SORT_DATE_CREATED);
        }
        else
        {
            return array();
        }
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryConnector#retrieve_external_repository_object()
     */
    function retrieve_external_repository_object($id)
    {
    	$file = $this->boxnet->get_file_info($id);
    	
    	$object = new BoxExternalRepositoryObject();
        $object->set_external_repository_id($this->get_external_repository_instance_id());
        $object->set_id($file['file_id']);
        $object->set_title($file['file_name']);
        $object->set_created($file['created']);
        $object->set_modified($file['modified']);                
        $object->set_rights($this->determine_rights());  
        return $object;
    }    

    function determine_rights()
    {
        $rights = array();
        $rights[ExternalRepositoryObject :: RIGHT_USE] = true;
        $rights[ExternalRepositoryObject :: RIGHT_EDIT] = false;
        $rights[ExternalRepositoryObject :: RIGHT_DELETE] = true;
        $rights[ExternalRepositoryObject :: RIGHT_DOWNLOAD] = true;
        return $rights;
    }
    
    function create_external_repository_object($file)
    {
    	return $this->boxnet->UploadFile($file);    	
    }

    /**
     * @param ContentObject $content_object
     * @return mixed
     */
    function export_external_repository_object($content_object)
    {
        return $this->boxnet->ExportFile($content_object->get_full_path());
    }    

    /**
     * @param string $id
     * @return mixed
     */
    function delete_external_repository_object($id)
    {
        return $this->boxnet->delete_file($id);
    }	 

    function download_external_repository_object($id)
    {
    	return $this->boxnet->download_file($id);
    }
    
	function update_external_repository_object($values)
    {
		
    }
}
?>
