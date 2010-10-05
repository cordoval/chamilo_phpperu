<?php
/**
 * $Id: personal_calendar_data_manager.class.php 127 2009-11-09 13:11:56Z vanpouckesven $
 * @package application.personal_calendar
 */
/**
 * This abstract class provides the necessary functionality to connect a
 * personal calendar to a storage system.
 */
class PersonalCalendarDataManager
{
    /**
     * Instance of the class, for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor. Initializes the data manager.
     */
    protected function PersonalCalendarDataManager()
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
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_personal_calendar_data_manager.class.php';
            $class = Utilities :: underscores_to_camelcase($type) . 'PersonalCalendarDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }
}
?>