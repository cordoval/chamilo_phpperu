<?php
/**
 * $Id: buddy_list_category.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @author Sven Vanpoucke
 * @package user.lib
 */
class BuddyListCategory extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_ID = 'id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_USER_ID = 'user_id';

    /**
     * Get the default properties of all users quota objects.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_TITLE, self :: PROPERTY_USER_ID));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return UserDataManager :: get_instance();
    }

    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    function set_title($title)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>