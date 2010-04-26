<?php
/**
 * $Id: distribute_data_manager.class.php 194 2009-11-13 11:54:13Z chellee $
 * @package application.lib.distribute
 */
/**
 * This is a skeleton for a data manager for the Distribute Application.
 * Data managers must extend this class and implement its abstract methods.
 *
 * @author Hans De Bisschop
 */
class DistributeDataManager
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
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_distribute_data_manager.class.php';
            $class = Utilities :: underscores_to_camelcase($type) . 'DistributeDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }
}
?>