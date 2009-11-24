<?php
/**
 * $Id: menu_data_manager.class.php 157 2009-11-10 13:44:02Z vanpouckesven $
 * @package menu.lib
 */
/**
 *	This is a skeleton for a data manager for the Users table.
 *	Data managers must extend this class and implement its abstract methods.
 *
 *	@author Hans De Bisschop
 *	@author Dieter De Neef
 */
abstract class MenuDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function MenuDataManager()
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
     * @return UserDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '.class.php';
            $class = $type . 'MenuDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    /**
     * Creates a storage unit
     * @param string $name Name of the storage unit
     * @param array $properties Properties of the storage unit
     * @param array $indexes The indexes which should be defined in the created
     * storage unit
     */
    abstract function create_storage_unit($name, $properties, $indexes);

    abstract function count_navigation_items($condition = null);

    abstract function retrieve_navigation_items($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function retrieve_navigation_item($id);

    abstract function retrieve_navigation_item_at_sort($parent, $sort, $direction);

    abstract function update_navigation_item($menuitem);

    abstract function delete_navigation_items($condition = null);
}
?>