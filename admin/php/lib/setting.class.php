<?php

/**
 * @package admin.lib
 * $Id: setting.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @author Hans De Bisschop
 */


class Setting extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_APPLICATION = 'application';
    const PROPERTY_VARIABLE = 'variable';
    const PROPERTY_VALUE = 'value';
    const PROPERTY_USER_SETTING = 'user_setting';

    /**
     * Get the default properties of all settings.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_APPLICATION, self :: PROPERTY_VARIABLE, self :: PROPERTY_VALUE,
        												  self :: PROPERTY_USER_SETTING));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return AdminDataManager :: get_instance();
    }

    /**
     * Returns the application of this setting object
     * @return string The setting application
     */
    function get_application()
    {
        return $this->get_default_property(self :: PROPERTY_APPLICATION);
    }

    /**
     * Returns the variable of this setting object
     * @return string the variable
     */
    function get_variable()
    {
        return $this->get_default_property(self :: PROPERTY_VARIABLE);
    }

    /**
     * Returns the value of this setting object
     * @return string the value
     */
    function get_value()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE);
    }

    /**
     * Sets the application of this setting.
     * @param string $application the setting application.
     */
    function set_application($application)
    {
        $this->set_default_property(self :: PROPERTY_APPLICATION, $application);
    }

    /**
     * Sets the variable of this setting.
     * @param string $variable the variable.
     */
    function set_variable($variable)
    {
        $this->set_default_property(self :: PROPERTY_VARIABLE, $variable);
    }

    /**
     * Sets the value of this setting.
     * @param string $value the value.
     */
    function set_value($value)
    {
        $this->set_default_property(self :: PROPERTY_VALUE, $value);
    }
    
	/**
     * Returns the user_setting of this setting object
     * @return string the user_setting
     */
    function get_user_setting()
    {
        return $this->get_default_property(self :: PROPERTY_USER_SETTING);
    }
    
	/**
     * Sets the user_setting of this setting.
     * @param string $user_setting the user_setting.
     */
    function set_user_setting($user_setting)
    {
        $this->set_default_property(self :: PROPERTY_USER_SETTING, $user_setting);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
    
    function delete()
    {
    	if (! parent :: delete())
    	{
    		return false;
    	}
    	else {
    		if ($this->get_user_setting())
			{
	    		$condition = new EqualityCondition(UserSetting::PROPERTY_SETTING_ID, $this->get_id());
	    		if (! UserDataManager::get_instance()->delete_user_settings($condition))
	    		{
	    			return false;
	    		}
	    		else 
	    		{
	    			return true;
	    		}
			}
			else 
			{
				return true;
			}
    	}
    }
}
?>