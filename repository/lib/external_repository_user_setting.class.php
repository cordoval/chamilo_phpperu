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
}
?>