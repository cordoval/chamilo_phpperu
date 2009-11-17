<?php
/**
 * $Id: user_view_rel_content_object.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib
 */

/**
 *  @author Sven Vanpoucke
 */

class UserViewRelContentObject extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_VIEW_ID = 'view_id';
    const PROPERTY_CONTENT_OBJECT_TYPE = 'content_object_type';
    const PROPERTY_VISIBILITY = 'visibility';

    /**
     * Get the default properties of all user_view_rel_content_objects.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_VIEW_ID, self :: PROPERTY_VISIBILITY, self :: PROPERTY_CONTENT_OBJECT_TYPE);
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return RepositoryDataManager :: get_instance();
    }

    /**
     * Returns the view_id of this user_view_rel_content_object.
     * @return int The view_id.
     */
    function get_view_id()
    {
        return $this->get_default_property(self :: PROPERTY_VIEW_ID);
    }

    /**
     * Returns the name of this user_view_rel_content_object.
     * @return String The name
     */
    function get_visibility()
    {
        return $this->get_default_property(self :: PROPERTY_VISIBILITY);
    }

    /**
     * Sets the user_view_rel_content_object_view_id of this user_view_rel_content_object.
     * @param int $user_view_rel_content_object_view_id The user_view_rel_content_object_view_id.
     */
    function set_view_id($view_id)
    {
        $this->set_default_property(self :: PROPERTY_VIEW_ID, $view_id);
    }

    /**
     * Sets the name of this user_view_rel_content_object.
     * @param String $name the name.
     */
    function set_visibility($visibility)
    {
        $this->set_default_property(self :: PROPERTY_VISIBILITY, $visibility);
    }

    function get_content_object_type()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_TYPE);
    }

    function set_content_object_type($content_object_type)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_TYPE, $content_object_type);
    }

    function create()
    {
        $gdm = RepositoryDataManager :: get_instance();
        return $gdm->create_user_view_rel_content_object($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>