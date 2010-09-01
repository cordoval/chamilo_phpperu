<?php
require_once dirname(__FILE__) . '/matterhorn_external_repository_object.class.php';
require_once dirname (__FILE__) . '/webservices/matterhorn_rest_client.class.php';

/**
 * 
 * @author magali.gillard
 *
 * Test login for Matterhorn : admin
 * Teste password for Matterhorn : opencast
 */
class MatterhornExternalRepositoryConnector extends ExternalRepositoryConnector
{
    private $matterhorn;
    private $login;
    private $password;
    
    const METHOD_POST = MatterhornRestClient :: METHOD_POST;
    const METHOD_GET = MatterhornRestClient :: METHOD_GET;
    
    function MatterhornExternalRepositoryConnector($external_repository_instance)
    {
    	parent :: __construct($external_repository_instance);
     	
//     	$this->login = ExternalRepositorySetting :: get('login', $this->get_external_repository_instance_id());
//     	$this->password = ExternalRepositorySetting :: get('password', $this->get_external_repository_instance_id());
    	
    	$url = ExternalRepositorySetting :: get('url', $this->get_external_repository_instance_id());
    	$this->matterhorn = new MatterhornRestClient($url);    	
    }

    function retrieve_media_file_content()
    {
    	
    }
    
    function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {
    	dump ($this->request(self :: METHOD_GET, '/search/rest/episode.xml'));  	  	
    }

    function retrieve_external_repository_object($id)
    {
        
    }
 
    function request($method, $url, $data) {
        if ($this->matterhorn) {
            return $this->matterhorn->request($method, $url, $data);
        }
        return false;
    }
    
    function count_external_repository_objects($condition)
    {
       
    }

    function delete_external_repository_object($id)
    {
    
    }

    function export_external_repository_object($object)
    {
        
        return true;
    }

    function determine_rights($video_entry)
    {
        $rights = array();
        $rights[ExternalRepositoryObject :: RIGHT_USE] = true;
        $rights[ExternalRepositoryObject :: RIGHT_EDIT] = ($video_entry->getEditLink() !== null ? true : false);
        $rights[ExternalRepositoryObject :: RIGHT_DELETE] = ($video_entry->getEditLink() !== null ? true : false);
        $rights[ExternalRepositoryObject :: RIGHT_DOWNLOAD] = false;
        return $rights;
    }
    
     /**
     * @param string $query
     * @return string
     */
    static function translate_search_query($query)
    {
        return $query;
    }
    
}
?>