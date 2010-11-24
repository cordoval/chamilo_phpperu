<?php
namespace common\extensions\external_repository_manager\implementation\dropbox;

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

use Dropbox_OAuth_PEAR;
use Dropbox_API;
use HTTP_OAuth;
require_once 'OAuth/Request.php';

require_once Path :: get_plugin_path() . 'dropbox-api/API.php';
require_once dirname(__FILE__) . '/dropbox_external_repository_object.class.php';


class DropboxExternalRepositoryConnector extends ExternalRepositoryConnector
{
    private $dropbox;
    private $consumer;
    private $key;
    private $secret;
    private $tokens;
    private $oauth;    

    const SORT_DATE_CREATED = 'date-created';    

    function __construct($external_repository_instance)
    {
        parent :: __construct($external_repository_instance);
        
        $this->key = ExternalRepositorySetting :: get('key', $this->get_external_repository_instance_id());
        $this->secret = ExternalRepositorySetting :: get('secret', $this->get_external_repository_instance_id());
        
        $this->oauth = new Dropbox_OAuth_PEAR($this->key, $this->secret);
        
    	if (isset($_SESSION['state'])) {
		    $state = $_SESSION['state'];
		} else {
		    $state = 1;
		}	
		switch($state) 
		{
			case 1 :
		        $this->tokens = $this->oauth->getRequestToken();
		        $url = $this->oauth->getAuthorizeUrl(Redirect:: current_url());		        
		        $_SESSION['state'] = 2;
		        $_SESSION['oauth_tokens'] = $this->tokens;
		        header('Location: ' . $url);
		        die();
		    case 2 :
		        
		    	$this->oauth->setToken($_SESSION['oauth_tokens']);
		        $this->tokens = $this->oauth->getAccessToken();
		        $_SESSION['state'] = 3;
		        $_SESSION['oauth_tokens'] = $this->tokens;	        		
		    case 3 :		    	
		        $this->oauth->setToken($_SESSION['oauth_tokens']);		        
		        break;
		}   
		$this->dropbox = new Dropbox_API($this->oauth);	        
	}      
	
    /**
     * @return string
     */
    function retrieve_user_id()
    {
        if (! isset($this->user_id))
        {
            $hidden = $this->dropbox->prefs_getHidden();
            $this->user_id = $hidden['nsid'];
        }
        return $this->user_id;
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
       	if(!is_null($folder))
       	{
       		$folder = urldecode($folder);
       	}
       	else $folder = '/';
    	
       	$files = $this->dropbox->getMetaData($this->encode($folder));        
        return $files;
    }
    
	function retrieve_folder($path, $condition = null, $order_property, $offset, $count)
    {    	
        $folders = $this->dropbox->getMetaData($path);
        return $folders;
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
        $files = $this->retrieve_files($condition, $order_property, $offset, $count);
        
        $objects = array();
        
        foreach ($files['contents'] as $file)
        {
            if($file['is_dir']!=1)
            {
            	$object = new DropboxExternalRepositoryObject();            
            	$object->set_id(substr($file['path'], 1));
            	$object->set_external_repository_id($this->get_external_repository_instance_id());
            	$object->set_title(substr($file['path'], strripos($file['path'], '/')+1));
            	$object->set_modified($file['modified']);
            	$object->set_type($file['icon']);
            	$object->set_description($file['size']);
            	$object->set_rights($this->determine_rights());
            	$objects[] = $object;
            }	
        }
        return new ArrayResultSet($objects);
    }
    
    function retrieve_folders($folder_url)
    {
    	$folders = array();
    	$files = $this->retrieve_folder('/', $condition, $order_property, $offset, $count);
    	foreach ($files['contents'] as $file)
        {
            if($file['is_dir'] == 1)
            {
            	$folder[] = array();
            	$folder['title'] = substr($file['path'], strripos($file['path'], '/')+1);
            	$folder['url'] = str_replace('__PLACEHOLDER__', substr($file['path'], 1), $folder_url);   
            	$folder['class'] = 'category';
            	$folder['sub'] =  $this->get_folder_tree($folder_url, $file['path']);
            	$folders[] = $folder;   	
            }            
        }
        return $folders;
    }

	function get_folder_tree($folder_url, $folder_path)
    {
        
        $folders = $this->retrieve_folder($this->encode($folder_path), $condition, $order_property, $offset, $count);
        $items = array();
    	foreach ($folders['contents'] as $child)
        {
            if($child['is_dir'] == 1)
            {
        		$sub_folder = array();
            	$sub_folder['title'] = substr($child['path'], strripos($child['path'], '/')+1);           
            	$sub_folder['url'] = str_replace('__PLACEHOLDER__', $child['path'], $folder_url);
            	$sub_folder['class'] = 'category';
            	$sub_folder['sub'] =  $this->get_folder_tree($folder_url, $child['path']);
				$items[] = $sub_folder;	
            }
        }        
        return $items;
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
        $feed_type = Request :: get(DropboxExternalRepositoryManager :: PARAM_FEED_TYPE);
        $query = ActionBarSearchForm :: get_query();

        if (($feed_type == DropboxExternalRepositoryManager :: FEED_TYPE_GENERAL && $query))
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
    	$id = str_replace(' ', '', $id);
    	$file = $this->dropbox->getMetaData($this->encode($id));
        
    	$object = new DropboxExternalRepositoryObject();
        $object->set_external_repository_id($this->get_external_repository_instance_id());
        $object->set_id($id);
        $object->set_title(str_replace('/', '', substr($id, strripos($id, '/'))));
        $object->set_modified($file['modified']);
        $object->set_type($file['icon']);
        $object->set_description($file['size']);
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

    /**
     * @param array $values
     * @param string $file_path
     * @return mixed
     */
    function create_external_repository_object($file, $file_path)
    {
    	$file = str_replace(' ', '', $file);
    	return $this->dropbox->putFile($file, $file_path);
    }

    /**
     * @param ContentObject $content_object
     * @return mixed
     */
    function export_external_repository_object($content_object)
    {
        $file = str_replace(' ', '', $content_object->get_title());
    	return $this->dropbox->putFile($file, $content_object->get_full_path());
    }    

    /**
     * @param string $id
     * @return mixed
     */
    function delete_external_repository_object($id)
    {
        return $this->dropbox->delete($id);
    }	 

    function download_external_repository_object($id)
    {
    	return $this->dropbox->getFile($this->encode($id));
    }
    
    function encode($path)
    {
    	$file = explode('/', $path);
    	$newpath = array();
        foreach($file as $f)
        {
        	$newpath[] = rawurlencode($f);        	
        }
        return implode('/', $newpath);
    }
}
?>
