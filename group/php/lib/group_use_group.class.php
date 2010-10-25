<?php

/**
 * @package admin.lib
 * $Id: group_use_group.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @author Hans De Bisschop
 */


class GroupUseGroup extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'use_group';

    const PROPERTY_USE_GROUP_ID = 'use_group_id';
    const PROPERTY_REQUEST_GROUP_ID = 'request_group_id';
    const PROPERTY_APPLICATION = 'application';

    /**
     * Get the default properties of all settings.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_USE_GROUP_ID, self :: PROPERTY_REQUEST_GROUP_ID, self :: PROPERTY_APPLICATION));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return GroupDataManager :: get_instance();
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
     * Returns the use_group_id of this setting object
     * @return string the use_group_id
     */
    function get_use_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_USE_GROUP_ID);
    }

    /**
     * Returns the request_group_id of this setting object
     * @return string the request_group_id
     */
    function get_request_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_REQUEST_GROUP_ID);
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
     * Sets the use_group_id of this setting.
     * @param string $use_group_id the use_group_id.
     */
    function set_use_group_id($use_group_id)
    {
        $this->set_default_property(self :: PROPERTY_USE_GROUP_ID, $use_group_id);
    }

    /**
     * Sets the request_group_id of this setting.
     * @param string $request_group_id the request_group_id.
     */
    function set_request_group_id($request_group_id)
    {
        $this->set_default_property(self :: PROPERTY_REQUEST_GROUP_ID, $request_group_id);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>