<?php
/**
 * $Id: webconference_user.class.php 220 2009-11-13 14:33:52Z kariboe $
 * @package application.lib.webconferencing
 */
/**
 * This class describes a WebconferenceUser data object
 * @author Michael Kyndt
 */
class WebconferenceUser extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'webconference_user';
    
    /**
     * WebconferenceUser properties
     */
    const PROPERTY_WEBCONFERENCE = 'webconference_id';
    const PROPERTY_USER = 'user_id';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_WEBCONFERENCE, self :: PROPERTY_USER);
    }

    function get_data_manager()
    {
        return WebconferencingDataManager :: get_instance();
    }

    /**
     * Returns the webconference of this WebconferenceUser.
     * @return the webconference.
     */
    function get_webconference()
    {
        return $this->get_default_property(self :: PROPERTY_WEBCONFERENCE);
    }

    /**
     * Sets the webconference of this WebconferenceUser.
     * @param webconference
     */
    function set_webconference($webconference)
    {
        $this->set_default_property(self :: PROPERTY_WEBCONFERENCE, $webconference);
    }

    /**
     * Returns the user of this WebconferenceUser.
     * @return the user.
     */
    function get_user()
    {
        return $this->get_default_property(self :: PROPERTY_USER);
    }

    /**
     * Sets the user of this WebconferenceUser.
     * @param user
     */
    function set_user($user)
    {
        $this->set_default_property(self :: PROPERTY_USER, $user);
    }

    function create()
    {
        return $this->get_data_manager()->create_webconference_user($this);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>