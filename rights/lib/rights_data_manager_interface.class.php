<?php
/**
 * @package help.lib
 *
 * This is an interface for a data manager for the Rights application.
 * Data managers must implement this class.
 *
 * @author Hans De Bisschop
 */
interface RightsDataManagerInterface
{

    function initialize();

    function create_rights_template_right_location($rights_template_right_location);

    /**
     * Creates a storage unit
     * @param string $name Name of the storage unit
     * @param array $properties Properties of the storage unit
     * @param array $indexes The indexes which should be defined in the created
     * storage unit
     */
    function create_storage_unit($name, $properties, $indexes);

    function retrieve_location_id_from_location_string($location);

    /**
     * retrieves the rights_template and right location
     *
     * @param int $right_id
     * @param int $rights_template_id
     * @param int $location_id
     * @return RightsTemplateRightLocation
     */
    function retrieve_rights_template_right_location($right, $rights_template_id, $location_id);

    function retrieve_location($id);

    function retrieve_right($id);

    function retrieve_rights_template($id);

    function retrieve_rights_templates($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_rights($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_locations($condition = null, $offset = null, $count = null, $order_property = null);

    function add_nested_values($location, $previous_visited, $number_of_elements = 1);

    function delete_location_nodes($location);

    function delete_nested_values($location);

    function move_location($location, $new_parent_id, $new_previous_id = 0);

    function update_rights_template($rights_template);

    function delete_rights_template($rights_template);

    function delete_locations($condition = null);

    function delete_orphaned_rights_template_right_locations();

    function retrieve_user_right_location($right_id, $user_id, $location_id);

    function retrieve_user_right_locations($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_group_right_location($right_id, $group_id, $location_id);

    function retrieve_group_right_locations($condition = null, $offset = null, $count = null, $order_property = null);

}
?>