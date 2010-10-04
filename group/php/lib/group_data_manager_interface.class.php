<?php
/**
 * @package group.lib
 *
 * This is an interface for a data manager for the Group application.
 * Data managers must implement this class.
 *
 * @author Hans De Bisschop
 */
interface GroupDataManagerInterface
{

    function initialize();

    function delete_group($group);

    function delete_group_rel_user($groupreluser);

    function update_group($group);

    function create_group($group);

    function create_group_rel_user($groupreluser);

    function create_storage_unit($name, $properties, $indexes);

    function count_groups($conditions = null);

    function count_group_rel_users($conditions = null);

    function retrieve_group($id);

    function truncate_group($id);

    function retrieve_groups($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_group_rel_user($user_id, $group_id);

    function retrieve_group_rel_users($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_user_groups($user_id);

    function retrieve_group_rights_templates($condition = null, $offset = null, $max_objects = null, $order_by = null);

    function delete_group_rights_templates($condition);

    function add_rights_template_link($group, $rights_template_id);

    function delete_rights_template_link($group, $rights_template_id);

    function update_rights_template_links($group, $rights_templates);

    function add_nested_values($previous_visited, $number_of_elements = 1);

    function delete_nested_values($group);

    function move_group($group, $new_parent_id, $new_previous_id = 0);
}
?>