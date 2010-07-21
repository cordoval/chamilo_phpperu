<?php
/**
 * $Id: external_repository_user_setting.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @author Sven Vanpoucke
 * @package user.lib
 */

class ExternalRepositoryUserSetting extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_SETTING_ID = 'setting_id';
    const PROPERTY_VALUE = 'value';
    
    /**
     * A static array containing all user settings of external repository instances
     * @var array
     */
    private static $settings;

    /**
     * Get the default properties of all users quota objects.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_USER_ID, self :: PROPERTY_SETTING_ID, self :: PROPERTY_VALUE));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return RepositoryDataManager :: get_instance();
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_setting_id()
    {
        return $this->get_default_property(self :: PROPERTY_SETTING_ID);
    }

    function set_setting_id($setting_id)
    {
        $this->set_default_property(self :: PROPERTY_SETTING_ID, $setting_id);
    }

    function get_value()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE);
    }

    function set_value($value)
    {
        $this->set_default_property(self :: PROPERTY_VALUE, $value);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    static function get_class_name()
    {
        return self :: CLASS_NAME;
    }

    /**
     * @param string $variable
     * @param int $external_repository_id
     * @return mixed
     */
    static function get($variable, $external_repository_id = null, $user_id = null)
    {
        if (is_null($external_repository_id) || ! is_numeric($external_repository_id))
        {
            $external_repository_id = Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY);
            
            if (is_null($external_repository_id) || ! is_numeric($external_repository_id))
            {
                Display :: error_page(Translation :: get('WhatsUpDoc'));
            }
        }
        
        if (is_null($user_id) || ! is_numeric($user_id))
        {
            $user_id = Session :: get_user_id();
        }
        
        if (! isset(self :: $settings[$external_repository_id][$user_id]))
        {
            self :: load($external_repository_id, $user_id);
        }
        
        return (isset(self :: $settings[$external_repository_id][$user_id][$variable]) ? self :: $settings[$external_repository_id][$user_id][$variable] : null);
    }

    static function get_all($external_repository_id = null, $user_id = null)
    {
        if (is_null($external_repository_id) || ! is_numeric($external_repository_id))
        {
            $external_repository_id = Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY);
            
            if (is_null($external_repository_id) || ! is_numeric($external_repository_id))
            {
                Display :: error_page(Translation :: get('WhatsUpDoc'));
            }
        }
        
        if (is_null($user_id) || ! is_numeric($user_id))
        {
            $user_id = Session :: get_user_id();
        }
        
        if (! isset(self :: $settings[$external_repository_id][$user_id]))
        {
            self :: load($external_repository_id, $user_id);
        }
        
        return self :: $settings[$external_repository_id][$user_id];
    }

    static function load($external_repository_id, $user_id)
    {
        $condition = new EqualityCondition(ExternalRepositorySetting :: PROPERTY_EXTERNAL_REPOSITORY_ID, $external_repository_id);
        $settings = RepositoryDataManager :: get_instance()->retrieve_external_repository_settings($condition);
        
        $setting_ids = array();
        while ($setting = $settings->next_result())
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(ExternalRepositoryUserSetting :: PROPERTY_USER_ID, $user_id);
            $conditions[] = new EqualityCondition(ExternalRepositoryUserSetting :: PROPERTY_SETTING_ID, $setting->get_id());
            $condition = new AndCondition($conditions);
            
            $user_settings = RepositoryDataManager :: get_instance()->retrieve_external_repository_user_settings($condition, array(), 0, 1);
            if ($user_settings->size() == 1)
            {
                $user_setting = $user_settings->next_result();
                self :: $settings[$external_repository_id][$user_id][$setting->get_variable()] = $user_setting->get_value();
            }
        }
    }
}
?>