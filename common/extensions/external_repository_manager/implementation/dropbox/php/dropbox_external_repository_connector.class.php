<?php
namespace common\extensions\external_repository_manager\implementation\dropbox;

use common\libraries\Request;
use common\libraries\Path;
use common\libraries\Session;
use common\libraries\Utilities;
use common\libraries\ArrayResultSet;

use common\extensions\external_repository_manager\ExternalRepositoryConnector;
use common\extensions\external_repository_manager\ExternalRepositoryObject;

use repository\RepositoryDataManager;
use repository\ExternalRepositorySetting;
use repository\ExternalRepositoryUserSetting;

require_once dirname(__FILE__) . '/dropbox_external_repository_object.class.php';
require_once 'OAuth/Request.php';
require_once Path :: get_plugin_path() . 'dropbox-api/API.php';


class DropboxExternalRepositoryConnector extends ExternalRepositoryConnector
{
    private $dropbox;
    private $consumer;
    private $key;
    private $secret;
    private $tokens;
    private $oauth;  
    

    function DropboxExternalRepositoryConnector($external_repository_instance)
    {
        parent :: __construct($external_repository_instance);
        
        $this->oauth = new Dropbox_OAuth_PHP($consumerKey, $consumerSecret);

    	$this->dropbox = new Dropbox_API($this->oauth);

		// For convenience, definitely not required
		header('Content-Type: text/plain');
				
		// There are multiple steps in this workflow, we keep a 'state number' here
		if (isset($_SESSION['state'])) {
		    $state = $_SESSION['state'];
		} else {
		    $state = 1;
		}
		
		switch($state) 
		{
		
		    /* In this phase we grab the initial request tokens
		       and redirect the user to the 'authorize' page hosted
		       on dropbox */
		    case 1 :
		        //echo "Step 1: Acquire request tokens\n";
		        $this->tokens = $this->oauth->getRequestToken();
		        //print_r($tokens);
		
		        // Note that if you want the user to automatically redirect back, you can
		        // add the 'callback' argument to getAuthorizeUrl.
		        //echo "Step 2: You must now redirect the user to:\n";
		        echo $this->oauth->getAuthorizeUrl() . "\n";
		        $_SESSION['state'] = 2;
		        $_SESSION['oauth_tokens'] = $this->tokens;
		        die();
		
		    /* In this phase, the user just came back from authorizing
		       and we're going to fetch the real access tokens */
		    case 2 :
		        echo "Step 3: Acquiring access tokens\n";
		        $this->oauth->setToken($_SESSION['oauth_tokens']);
		        $this->tokens = $this->oauth->getAccessToken();
		        print_r($this->tokens);
		        $_SESSION['state'] = 3;
		        $_SESSION['oauth_tokens'] = $this->tokens;
		        // There is no break here, intentional
		
		    /* This part gets called if the authentication process
		       already succeeded. We can use our stored tokens and the api 
		       should work. Store these tokens somewhere, like a database */
		    case 3 :
		        echo "The user is authenticated\n";
		        echo "You should really save the oauth tokens somewhere, so the first steps will no longer be needed\n";
		        print_r($_SESSION['oauth_tokens']);
		        $this->oauth->setToken($_SESSION['oauth_tokens']);
		        break;
		}

	}   
    
	function count_external_repository_objects($condition)
	{
		
    }
    
	function delete_external_repository_object($id)
    {
		//$this->dropbox->delete($id);
    }
    
	function export_external_repository_object($object)
    {
 		//$this->dropbox->putFile($object);
    }
    
	function retrieve_external_repository_object($id)
    {

    }
    
    function create_external_repository_object($values, $track_path)
    {
    	
    }
    
	function determine_rights($video_entry)
    {
        $rights = array();
        $rights[ExternalRepositoryObject :: RIGHT_USE] = true;
        $rights[ExternalRepositoryObject :: RIGHT_EDIT] = false;
        $rights[ExternalRepositoryObject :: RIGHT_DELETE] = true;
        $rights[ExternalRepositoryObject :: RIGHT_DOWNLOAD] = false;
        return $rights;
    }
    
	static function translate_search_query($query)
    {
        return $query;
    }
    
	static function get_sort_properties()
    {
        
    }
    
}
?>