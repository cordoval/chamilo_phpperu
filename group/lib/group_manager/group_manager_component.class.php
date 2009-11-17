<?php
/**
 * $Id: group_manager_component.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_manager
 */
/**
 * Base class for a user manager component.
 * A user manager provides different tools to the end user. Each tool is
 * represented by a user manager component and should extend this class.
 */

abstract class GroupManagerComponent extends CoreApplicationComponent
{

    /**
     * Constructor
     * @param GroupsManager $groups_manager The user manager which
     * provides this component
     */
    function GroupManagerComponent($group_manager)
    {
        parent :: __construct($group_manager);
    }

    function retrieve_groups($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_groups($condition, $offset, $count, $order_property);
    }

    function retrieve_group_rel_users($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_group_rel_users($condition, $offset, $count, $order_property);
    }

    function retrieve_group_rel_user($user_id, $group_id)
    {
        return $this->get_parent()->retrieve_group_rel_user($user_id, $group_id);
    }

    function count_groups($conditions = null)
    {
        return $this->get_parent()->count_groups($conditions);
    }

    function count_group_rel_users($conditions = null)
    {
        return $this->get_parent()->count_group_rel_users($conditions);
    }

    function get_search_condition()
    {
        return $this->get_parent()->get_search_condition();
    }

    function get_user_search_condition()
    {
        return $this->get_parent()->get_user_search_condition();
    }

    function display_user_search_form()
    {
        return $this->get_parent()->display_user_search_form();
    }

    function retrieve_group($id)
    {
        return $this->get_parent()->retrieve_group($id);
    }

    function get_search_validate()
    {
        return $this->get_parent()->get_search_validate();
    }

    /**
     * @see GroupsManager::force_menu_url()
     */
    function force_menu_url($url)
    {
        return $this->get_parent()->force_menu_url($url);
    }

    function get_group_editing_url($group)
    {
        return $this->get_parent()->get_group_editing_url($group);
    }

    function get_create_group_url($parent)
    {
        return $this->get_parent()->get_create_group_url($parent);
    }

    function get_group_emptying_url($group)
    {
        return $this->get_parent()->get_group_emptying_url($group);
    }

    function get_group_viewing_url($group)
    {
        return $this->get_parent()->get_group_viewing_url($group);
    }

    function get_manage_group_rights_url($group)
    {
        return $this->get_parent()->get_manage_group_rights_url($group);
    }

    function get_group_rel_user_unsubscribing_url($groupreluser)
    {
        return $this->get_parent()->get_group_rel_user_unsubscribing_url($groupreluser);
    }

    function get_group_rel_user_subscribing_url($group, $user)
    {
        return $this->get_parent()->get_group_rel_user_subscribing_url($group, $user);
    }

    function get_group_suscribe_user_browser_url($group)
    {
        return $this->get_parent()->get_group_suscribe_user_browser_url($group);
    }

    function get_group_delete_url($group)
    {
        return $this->get_parent()->get_group_delete_url($group);
    }

    function get_import_url()
    {
        return $this->get_parent()->get_import_url();
    }

    function get_move_group_url($group)
    {
        return $this->get_parent()->get_move_group_url($group);
    }

    function get_export_url()
    {
        return $this->get_parent()->get_export_url();
    }

    function get_manage_rights_templates_url($group)
    {
        return $this->get_parent()->get_manage_rights_templates_url($group);
    }
}
?>