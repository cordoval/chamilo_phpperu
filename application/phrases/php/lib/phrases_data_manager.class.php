<?php
namespace application\phrases;

use common\libraries\WebApplication;
use common\libraries\Configuration;
use common\libraries\Utilities;
/**
 * $Id: phrases_data_manager.class.php 127 2009-11-09 13:11:56Z vanpouckesven $
 * @package application.personal_calendar
 */
/**
 * This abstract class provides the necessary functionality to connect a
 * personal calendar to a storage system.
 */
class PhrasesDataManager
{
    /**
     * Instance of the class, for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor. Initializes the data manager.
     */
    protected function PhrasesDataManager()
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
            require_once WebApplication::get_application_class_lib_path('phrases') . 'data_manager/' . strtolower($type) . '_phrases_data_manager.class.php';
            $class = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($type) . 'PhrasesDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }
}
?>