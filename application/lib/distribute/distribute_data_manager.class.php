<?php
/**
 * $Id: distribute_data_manager.class.php 194 2009-11-13 11:54:13Z chellee $
 * @package application.lib.distribute
 */
/**
 *	This is a skeleton for a data manager for the Distribute Application.
 *	Data managers must extend this class and implement its abstract methods.
 *
 *	@author Hans De Bisschop
 */
abstract class DistributeDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function DistributeDataManager()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return DistributeDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . Utilities :: camelcase_to_underscores($type) . '.class.php';
            $class = $type . 'DistributeDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    abstract function initialize();

    abstract function create_storage_unit($name, $properties, $indexes);

    abstract function create_announcement_distribution($announcement_distribution);

    abstract function update_announcement_distribution($announcement_distribution);

    abstract function delete_announcement_distribution($announcement_distribution);

    abstract function count_announcement_distributions($conditions = null);

    abstract function retrieve_announcement_distribution($id);

    abstract function retrieve_announcement_distributions($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function retrieve_announcement_distribution_target_groups($announcement_distribution);

    abstract function retrieve_announcement_distribution_target_users($announcement_distribution);
}
?>