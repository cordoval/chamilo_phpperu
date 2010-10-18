<?php
/**
 * $Id: webconference_group.class.php 220 2009-11-13 14:33:52Z kariboe $
 * @package application.lib.webconferencing
 */
/**
 * This class describes a WebconferenceGroup data object
 * @author Michael Kyndt
 */
class WebconferenceGroup extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'webconference_group';
    
    /**
     * WebconferenceGroup properties
     */
    const PROPERTY_WEBCONFERENCE = 'webconference_id';
    const PROPERTY_GROUP_ID = 'group_id';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_WEBCONFERENCE, self :: PROPERTY_GROUP_ID);
    }

    function get_data_manager()
    {
        return WebconferencingDataManager :: get_instance();
    }

    /**
     * Returns the webconference of this WebconferenceGroup.
     * @return the webconference.
     */
    function get_webconference()
    {
        return $this->get_default_property(self :: PROPERTY_WEBCONFERENCE);
    }

    /**
     * Sets the webconference of this WebconferenceGroup.
     * @param webconference
     */
    function set_webconference($webconference)
    {
        $this->set_default_property(self :: PROPERTY_WEBCONFERENCE, $webconference);
    }

    /**
     * Returns the group_id of this WebconferenceGroup.
     * @return the group_id.
     */
    function get_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_GROUP_ID);
    }

    /**
     * Sets the group_id of this WebconferenceGroup.
     * @param group_id
     */
    function set_group_id($group_id)
    {
        $this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
    }

    function create()
    {
        return $this->get_data_manager()->create_webconference_group($this);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>