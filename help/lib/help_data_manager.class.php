<?php
/**
 * $Id: help_data_manager.class.php 226 2009-11-13 14:44:03Z chellee $
 * @package help.lib
 */
abstract class HelpDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function HelpDataManager()
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
            $class = $type . 'HelpDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    abstract function initialize();

    abstract function update_help_item($help_item);

    abstract function create_help_item($help_item);

    abstract function count_help_items($conditions = null);

    abstract function retrieve_help_item($id);

    abstract function retrieve_help_items($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function create_storage_unit($name, $properties, $indexes);

    abstract function get_next_help_item_id();

    abstract function retrieve_help_item_by_name_and_language($name, $language);
}
?>