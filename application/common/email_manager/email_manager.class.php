<?php
/**
 * $Id: email_manager.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.email_manager
 */

class EmailManager extends SubManager
{
    const ACTION_EMAIL = 'emailer';

    const DEFAULT_ACTION = self :: ACTION_EMAIL;

	const PARAM_EMAIL_MANAGER_ACTION = 'eaction';

	private $target_users;

    function EmailManager($parent)
    {
        parent :: __construct($parent);

        $this->target_users = array();

        $email_action = Request :: get(self :: PARAM_EMAIL_MANAGER_ACTION);
        if ($email_action)
        {
            $this->set_parameter(self :: PARAM_EMAIL_MANAGER_ACTION, $email_action);
        }
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

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_EMAIL_MANAGER_ACTION;
    }

    /**
     * @param Application $application
     * @return EmailManager
     */
    static function construct($application)
    {
        return parent :: construct(__CLASS__, $application);
    }

    /**
     * @param Application $application
     */
    static function launch($application)
    {
        self :: construct(__CLASS__, $application)->run();
    }

}
?>