<?php
/**
 * $Id: laika_data_manager.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika
 */

abstract class LaikaDataManager
{
    /**
     * Instance of the class, for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor. Initializes the data manager.
     */
    protected function LaikaDataManager()
    {
        $this->initialize();
    }

    /**
     * Creates the shared instance of the configured data manager if
     * necessary and returns it. Uses a factory pattern.
     * @return PersonalCalendarDataManager The instance.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_laika_data_manager.class.php';
            $class = $type . 'LaikaDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }
}
?>