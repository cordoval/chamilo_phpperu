<?php
/**
 * $Id: rights_data_manager.class.php 157 2009-11-10 13:44:02Z vanpouckesven $
 * @package rights.lib
 * @author Hans de Bisschop
 */
/**
 *	This is a skeleton for a data manager for the Rights table.
 *	Data managers must extend this class and implement its abstract methods.
 *
 *	@author Hans De Bisschop
 *	@author Dieter De Neef
 */
abstract class RightsDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function RightsDataManager()
    {
        $this->initialize();
    }

    /**
     * Initializes the data manager.
     */
    abstract function initialize();

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return RightsDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '.class.php';
            $class = $type . 'RightsDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    abstract function create_rights_templaterightlocation($rights_templaterightlocation);

    /**
     * Creates a storage unit
     * @param string $name Name of the storage unit
     * @param array $properties Properties of the storage unit
     * @param array $indexes The indexes which should be defined in the created
     * storage unit
     */
    abstract function create_storage_unit($name, $properties, $indexes);

    abstract function retrieve_location_id_from_location_string($location);

    /**
     * retrieves the rights_template and right location
     *
     * @param int $right_id
     * @param int $rights_template_id
     * @param int $location_id
     * @return RightsTemplateRightLocation
     */
    abstract function retrieve_rights_template_right_location($right, $rights_template_id, $location_id);

    abstract function retrieve_location($id);

    abstract function retrieve_right($id);

    abstract function retrieve_rights_template($id);

    abstract function retrieve_rights_templates($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function retrieve_rights($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function retrieve_locations($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function add_nested_values($location, $previous_visited, $number_of_elements = 1);

    abstract function delete_location_nodes($location);

    abstract function delete_nested_values($location);

    abstract function move_location($location, $new_parent_id, $new_previous_id = 0);

    abstract function update_rights_template($rights_template);

    abstract function delete_rights_template($rights_template);

    abstract function delete_locations($condition = null);

    abstract function delete_orphaned_rights_template_right_locations();

    //abstract function retrieve_shared_content_objects($rights_templates,$rights);
    

    abstract function retrieve_user_right_location($right_id, $user_id, $location_id);

    abstract function retrieve_group_right_location($right_id, $group_id, $location_id);
}
?>