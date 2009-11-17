<?php
/**
 * $Id: rights_manager_component.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.rights_manager
 */

/**
 * Base class for a user manager component.
 * A user manager provides different tools to the end user. Each tool is
 * represented by a user manager component and should extend this class.
 */

abstract class RightsManagerComponent extends CoreApplicationComponent
{

    function RightsManagerComponent($rights_manager)
    {
        parent :: __construct($rights_manager);
    }

    function retrieve_users($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_users($condition, $offset, $count, $order_property);
    }

    function retrieve_groups($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_groups($condition, $offset, $count, $order_property);
    }

    function retrieve_rights_templates($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_rights_templates($condition, $offset, $count, $order_property);
    }

    function retrieve_rights_template($id)
    {
        return $this->get_parent()->retrieve_rights_template($id);
    }

    function retrieve_rights($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_rights($condition, $offset, $count, $order_property);
    }

    function retrieve_locations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_locations($condition, $offset, $count, $order_property);
    }

    function retrieve_rights_template_right_location($right_id, $rights_template_id, $location_id)
    {
        return $this->get_parent()->retrieve_rights_template_right_location($right_id, $rights_template_id, $location_id);
    }

    function retrieve_user_rights_template($user_id, $location_id)
    {
        return $this->get_parent()->retrieve_user_rights_template($user_id, $location_id);
    }

    function retrieve_group_rights_template($group_id, $location_id)
    {
        return $this->get_parent()->retrieve_group_rights_template($group_id, $location_id);
    }

    function retrieve_location($location_id)
    {
        return $this->get_parent()->retrieve_location($location_id);
    }

    function count_users($conditions = null)
    {
        return $this->get_parent()->count_users($conditions);
    }

    function delete_rights_template($rights_template)
    {
        return $this->get_parent()->delete_rights_template($rights_template);
    }

    function count_groups($conditions = null)
    {
        return $this->get_parent()->count_groups($conditions);
    }

    function count_rights_templates($conditions = null)
    {
        return $this->get_parent()->count_rights_templates($conditions);
    }

    function count_locations($conditions = null)
    {
        return $this->get_parent()->count_locations($conditions);
    }

    /**
     * @see RightsManager::retrieve_user()
     */
    function retrieve_user($id)
    {
        return $this->get_parent()->retrieve_user($id);
    }

    function retrieve_group($id)
    {
        return $this->get_parent()->retrieve_group($id);
    }

    /**
     * @see RightsManager::User_deletion_allowed()
     */
    function user_deletion_allowed($user)
    {
        return $this->get_parent()->user_deletion_allowed($user);
    }

    /**
     * @see RightsManager::get_user_editing_url()
     */
    function get_user_editing_url($user)
    {
        return $this->get_parent()->get_user_editing_url($user);
    }

    function get_group_editing_url($group)
    {
        return $this->get_parent()->get_group_editing_url($group);
    }

    /**
     * @see RightsManager::get_user_quota_url()
     */
    function get_user_quota_url($user)
    {
        return $this->get_parent()->get_user_quota_url($user);
    }

    function get_user_rights_templates_url($user)
    {
        return $this->get_parent()->get_user_rights_templates_url($user);
    }

    function get_group_rights_templates_url($group)
    {
        return $this->get_parent()->get_group_rights_templates_url($group);
    }

    function retrieve_user_right_location($right_id, $user_id, $location_id)
    {
        return $this->get_parent()->retrieve_user_right_location($right_id, $user_id, $location_id);
    }

    function retrieve_group_right_location($right_id, $group_id, $location_id)
    {
        return $this->get_parent()->retrieve_group_right_location($right_id, $group_id, $location_id);
    }
}
?>