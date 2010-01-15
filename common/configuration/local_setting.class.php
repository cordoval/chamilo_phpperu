<?php
/**
 * $Id: local_setting.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package common.configuration
 */
/**
 *	This class represents the current configurable settings.
 *	They are retrieved from the DB via the AdminDataManager
 *
 *	@author Sven Vanpoucke
 */

class LocalSetting
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;
    
    /**
     * Parameters defined in the configuration. Stored as an associative array.
     */
    private $params;

    /**
     * Constructor.
     */
    private function LocalSetting()
    {
        $this->params = $this->load_local_settings();
    }

    /**
     * Returns the instance of this class.
     * @return Configuration The instance.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self();
        }
        return self :: $instance;
    }

    /**
     * Gets a parameter from the configuration.
     * @param string $section The name of the section in which the parameter
     *                        is located.
     * @param string $name The parameter name.
     * @return mixed The parameter value.
     */
    function get($variable, $application = 'admin')
    {
        $instance = self :: get_instance();
        
        $params = $instance->params;
        
        if(!$params)
        {
        	return PlatformSetting :: get($variable, $application);
        }
        
        if (isset($params[$application]))
        {
            $value = $instance->params[$application][$variable];
            return (isset($value) ? $value : null);
        }
        else
        {
            return PlatformSetting :: get($variable, $application);
        }
    }

    function load_local_settings()
    {
        $user_id = Session :: get_user_id();
        if(!$user_id)
        {
        	return null;
        }
        
        $params = array();
        
        $condition = new EqualityCondition(UserSetting :: PROPERTY_USER_ID, $user_id);
        $user_settings = UserDataManager :: get_instance()->retrieve_user_settings($condition);
        while($user_setting = $user_settings->next_result())
        {
        	$condition = new EqualityCondition(Setting :: PROPERTY_ID, $user_setting->get_setting_id());
        	$setting = AdminDataManager :: get_instance()->retrieve_settings($condition);
        	$params[$setting->get_application()][$setting->get_variable()] = $user_setting->get_value();
        }
        
        return $params;
    }
}
?>