<?php
require_once dirname(__FILE__) . '/matterhorn_external_repository_object.class.php';

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
    
    function MatterhornExternalRepositoryConnector($external_repository_instance)
    {
    	parent :: __construct($external_repository_instance);
     	
     	$this->login = ExternalRepositorySetting :: get('login', $this->get_external_repository_instance_id());
     	$this->password = ExternalRepositorySetting :: get('passeword', $this->get_external_repository_instance_id());
     	//

     	$session_token = ExternalRepositoryUserSetting::get('session_token', $this->get_external_repository_instance_id());
    	if (! $session_token)
    	{
            exit(Translation :: get('Connection to Matterhorn server failed'));
        }
    }
    

	function retrieve_chamilo_user($user_id)
    {
        $udm = UserDataManager :: get_instance();
        
        if (! $this->chamilo_user || ($user_id != $this->chamilo_user->get_id()))
        {
            $this->chamilo_user = $udm->retrieve_user($user_id);
        }
        return $this->chamilo_user;
    }
    
    function create_matterhorn_user($chamilo_user_id)
    {
    	
    }
    
	/*
     * @param int $user_id
     * @return simplexmlobject user
     */
    function retrieve_mediamosa_user($chamilo_user_id)
    {
        if ($response = $this->request(self :: METHOD_GET, '/user/' . $chamilo_user_id))
        {
            if ($response->check_result())
            {
                return $response->get_response_content_xml()->items->item;
            }
        }
        
        return false;
    }
    
    function login()
    {
    	$url = ExternalRepositorySetting :: get('url', $this->get_external_repository_instance_id());
        $this->matterhorn = new MatterhornRestClient($url);
    }
    
    function retrieve_media_file_content()
    {
    	
    }
    
    function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {

    }

    function retrieve_external_repository_object($id)
    {
        
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