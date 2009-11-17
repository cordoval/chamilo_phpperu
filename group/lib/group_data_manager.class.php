<?php
/**
 * $Id: group_data_manager.class.php 157 2009-11-10 13:44:02Z vanpouckesven $
 * @package group.lib
 */
/**
 *	This is a skeleton for a data manager for the Users table.
 *	Data managers must extend this class and implement its abstract methods.
 *
 *	@author Hans De Bisschop
 *	@author Dieter De Neef
 */
abstract class GroupDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;
    
    /**
     * Array which contains the registered applications running on top of this
     * repositorydatamanager
     */
    private $applications;

    /**
     * Constructor.
     */
    protected function GroupDataManager()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return GroupsDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '.class.php';
            $class = $type . 'GroupDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    abstract function initialize();

    abstract function get_next_group_id();

    abstract function delete_group($group);

    abstract function delete_group_rel_user($groupreluser);

    abstract function update_group($group);

    abstract function create_group($group);

    abstract function create_group_rel_user($groupreluser);

    abstract function create_storage_unit($name, $properties, $indexes);

    abstract function count_groups($conditions = null);

    abstract function count_group_rel_users($conditions = null);

    abstract function retrieve_group($id);

    abstract function truncate_group($id);

    abstract function retrieve_groups($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function retrieve_group_rel_user($user_id, $group_id);

    abstract function retrieve_group_rel_users($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function retrieve_user_groups($user_id);

    abstract function retrieve_group_rights_templates($condition = null, $offset = null, $max_objects = null, $order_by = null);

    abstract function delete_group_rights_templates($condition);

    abstract function add_rights_template_link($group, $rights_template_id);

    abstract function delete_rights_template_link($group, $rights_template_id);

    abstract function update_rights_template_links($group, $rights_templates);

    abstract function add_nested_values($previous_visited, $number_of_elements = 1);

    abstract function delete_nested_values($group);

    abstract function move_group($group, $new_parent_id, $new_previous_id = 0);
}
?>