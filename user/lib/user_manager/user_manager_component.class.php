<?php
/**
 * $Id: user_manager_component.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager
 */
/**
 * Base class for a user manager component.
 * A user manager provides different tools to the end user. Each tool is
 * represented by a user manager component and should extend this class.
 */
abstract class UserManagerComponent extends CoreApplicationComponent
{

    /**
     * Constructor
     * @param UserManager $user_manager The user manager which
     * provides this component
     */
    function UserManagerComponent($user_manager)
    {
        parent :: __construct($user_manager);
    }

    /**
     * @see UserManager::retrieve_users()
     */
    function retrieve_users($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_users($condition, $offset, $count, $order_property);
    }

    /**
     * @see UserManager::count_users()
     */
    function count_users($conditions = null)
    {
        return $this->get_parent()->count_users($conditions);
    }

    /**
     * @see UserManager::retrieve_user()
     */
    function retrieve_user($id)
    {
        return $this->get_parent()->retrieve_user($id);
    }

    /**
     * @see UserManager::User_deletion_allowed()
     */
    function user_deletion_allowed($user)
    {
        return $this->get_parent()->user_deletion_allowed($user);
    }

    /**
     * @see UserManager::get_user_editing_url()
     */
    function get_user_editing_url($user)
    {
        return $this->get_parent()->get_user_editing_url($user);
    }

    /**
     * @see UserManager::get_user_quota_url()
     */
    function get_user_quota_url($user)
    {
        return $this->get_parent()->get_user_quota_url($user);
    }

    /**
     * @see UserManager::get_user_delete_url()
     */
    function get_user_delete_url($user)
    {
        return $this->get_parent()->get_user_delete_url($user);
    }

    function get_change_user_url($user)
    {
        return $this->get_parent()->get_change_user_url($user);
    }

    function get_manage_user_rights_url($user)
    {
        return $this->get_parent()->get_manage_user_rights_url($user);
    }

    function get_create_buddylist_category_url()
    {
        return $this->get_parent()->get_create_buddylist_category_url();
    }

    function get_delete_buddylist_category_url($category_id)
    {
        return $this->get_parent()->get_delete_buddylist_category_url($category_id);
    }

    function get_update_buddylist_category_url($category_id)
    {
        return $this->get_parent()->get_update_buddylist_category_url($category_id);
    }

    function get_create_buddylist_item_url()
    {
        return $this->get_parent()->get_create_buddylist_item_url();
    }

    function get_delete_buddylist_item_url($item_id)
    {
        return $this->get_parent()->get_delete_buddylist_item_url($item_id);
    }

    function get_change_buddylist_item_status_url($item_id, $status)
    {
        return $this->get_parent()->get_change_buddylist_item_status_url($item_id, $status);
    }

    function get_reporting_url($classname, $params)
    {
        return $this->get_parent()->get_reporting_url($classname, $params);
    }
    
	function get_user_detail_url($user_id)
    {
    	return $this->get_parent()->get_user_detail_url($user_id);
    }
}

?>