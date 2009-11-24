<?php
/**
 * $Id: alexia_data_manager.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.alexia
 */
abstract class AlexiaDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function AlexiaDataManager()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return AlexiaDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . Utilities :: camelcase_to_underscores($type) . '.class.php';
            $class = $type . 'AlexiaDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    abstract function initialize();

    abstract function create_storage_unit($name, $properties, $indexes);

    abstract function create_alexia_publication($alexia_publication);

    abstract function update_alexia_publication($alexia_publication);

    abstract function delete_alexia_publication($alexia_publication);

    abstract function count_alexia_publications($conditions = null);

    abstract function retrieve_alexia_publication($id);

    abstract function retrieve_alexia_publications($condition = null, $offset = null, $count = null, $order_property = array());

    abstract function create_alexia_publication_group($alexia_publication_group);

    abstract function delete_alexia_publication_group($alexia_publication_group);

    abstract function count_alexia_publication_groups($conditions = null);

    abstract function retrieve_alexia_publication_groups($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function create_alexia_publication_user($alexia_publication_user);

    abstract function delete_alexia_publication_user($alexia_publication_user);

    abstract function count_alexia_publication_users($conditions = null);

    abstract function retrieve_alexia_publication_users($condition = null, $offset = null, $count = null, $order_property = null);

}
?>