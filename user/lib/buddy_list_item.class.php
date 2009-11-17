<?php
/**
 * $Id: buddy_list_item.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @author Sven Vanpoucke
 * @package user.lib
 */


class BuddyListItem extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_BUDDY_ID = 'buddy_id';
    const PROPERTY_CATEGORY_ID = 'category_id';
    const PROPERTY_STATUS = 'status';
    
    const STATUS_NORMAL = 0;
    const STATUS_REQUESTED = 1;
    const STATUS_REJECTED = 2;

    /**
     * Get the default properties of all users quota objects.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_USER_ID, self :: PROPERTY_BUDDY_ID, self :: PROPERTY_CATEGORY_ID, self :: PROPERTY_STATUS);
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return UserDataManager :: get_instance();
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_buddy_id()
    {
        return $this->get_default_property(self :: PROPERTY_BUDDY_ID);
    }

    function set_buddy_id($buddy_id)
    {
        $this->set_default_property(self :: PROPERTY_BUDDY_ID, $buddy_id);
    }

    function get_category_id()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY_ID);
    }

    function set_category_id($category_id)
    {
        $this->set_default_property(self :: PROPERTY_CATEGORY_ID, $category_id);
    }

    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    function create()
    {
        $udm = UserDataManager :: get_instance();
        return $udm->create_buddy_list_item($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

}
?>