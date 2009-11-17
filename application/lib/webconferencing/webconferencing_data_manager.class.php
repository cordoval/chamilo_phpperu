<?php
/**
 * $Id: webconferencing_data_manager.class.php 220 2009-11-13 14:33:52Z kariboe $
 * @package application.lib.webconferencing
 */
/**
 *	This is a skeleton for a data manager for the Webconferencing Application.
 *	Data managers must extend this class and implement its abstract methods.
 *
 *	@author Stefaan Vanbillemont
 */
abstract class WebconferencingDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function WebconferencingDataManager()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return WebconferencingDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . Utilities :: camelcase_to_underscores($type) . '.class.php';
            $class = $type . 'WebconferencingDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    abstract function initialize();

    abstract function create_storage_unit($name, $properties, $indexes);

    abstract function get_next_webconference_id();

    abstract function create_webconference($webconference);

    abstract function update_webconference($webconference);

    abstract function delete_webconference($webconference);

    abstract function count_webconferences($conditions = null);

    abstract function retrieve_webconference($id);

    abstract function retrieve_webconferences($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function get_next_webconference_option_id();

    abstract function create_webconference_option($webconference_option);

    abstract function update_webconference_option($webconference_option);

    abstract function delete_webconference_options($webconference);

    abstract function delete_webconference_option($webconference_option);

    abstract function count_webconference_options($conditions = null);

    abstract function retrieve_webconference_option($id);

    abstract function retrieve_webconference_options($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function retrieve_webconference_groups($condition = null, $offset = null, $max_objects = null, $order_by = null);

    abstract function retrieve_webconference_users($condition = null, $offset = null, $max_objects = null, $order_by = null);

    abstract function create_webconference_user($webconference_user);

    abstract function create_webconference_group($webconference_group);
}
?>