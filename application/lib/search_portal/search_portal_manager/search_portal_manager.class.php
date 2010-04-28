<?php
/**
 * $Id: search_portal_manager.class.php 222 2009-11-13 14:39:28Z chellee $
 * @package application.search_portal.search_portal_manager
 */
require_once dirname(__FILE__) . '/../search_portal_block.class.php';

class SearchPortalManager extends WebApplication
{
    const APPLICATION_NAME = 'search_portal';
    
    const PARAM_USER = 'user';
    
	const ACTION_SEARCH = 'search';
	const ACTION_EMAIL_USER = 'emailer';
	
    function SearchPortalManager($user = null)
    {
        parent :: __construct($user);
    }

    /*
	 * Inherited.
	 */
    function run()
    {
        $action = $this->get_action();
        $component = null;
        switch ($action)
        {
            case self :: ACTION_SEARCH :
                $component = $this->create_component('Searcher');
                break;
            case self :: ACTION_EMAIL_USER :
            	$component = $this->create_component('Emailer');
            	break;
            default :
                $this->set_action(self :: ACTION_SEARCH);
                $component = $this->create_component('Searcher');
        }
        $component->run();
    }

	function get_email_user_url($user_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EMAIL_USER, self :: PARAM_USER => $user_id));
	}    
    
	function get_application_name() 
	{
		return self :: APPLICATION_NAME;
	}

}
?>