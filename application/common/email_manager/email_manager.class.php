<?php
/**
 * $Id: email_manager.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.email_manager
 */
require_once dirname(__FILE__) . '/email_manager_component.class.php';

class EmailManager extends SubManager
{
    const ACTION_EMAIL = 'email';
	const PARAM_EMAIL_MANAGER_ACTION = 'eaction';
	
	private $target_users;
	
    function EmailManager($parent, $target_users = array())
    {
        parent :: __construct($parent);
        
        $this->target_users = $target_users;
        
        $email_action = Request :: get(self :: PARAM_EMAIL_MANAGER_ACTION);
        if ($email_action)
        {
            $this->set_parameter(self :: PARAM_EMAIL_MANAGER_ACTION, $email_action);
        }
    }

    function run()
    {
        $action = $this->get_parameter(self :: PARAM_EMAIL_MANAGER_ACTION);
        
        switch ($action)
        {
            case self :: ACTION_EMAIL :
                $component = EmailManagerComponent :: factory('Emailer', $this);
                break;
            default :
                $component = EmailManagerComponent :: factory('Emailer', $this);
                break;
        }
        
        $component->run();
    }

    function set_target_users($target_users)
    {
    	$this->target_users = $target_users;
    }
    
    function get_target_users()
    {
    	return $this->target_users;
    }
    
	function get_application_component_path() 
	{
		return Path :: get_application_library_path() . 'email_manager/component/';
	}

}
?>