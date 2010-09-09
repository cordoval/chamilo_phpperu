<?php
/**
 * $Id: tracking_data_manager.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package tracking.lib
 */

/**
 *	This is a skeleton for a data manager for tracking manager
 *	Data managers must extend this class and implement its abstract methods.
 *	If the user configuration dictates that the "database" data manager is to
 *	be used, this class will automatically attempt to instantiate
 *	"DatabaseTrackingDataManager"; hence, this naming convention must be
 *	respected for all extensions of this class.
 *
 *	@author Sven Vanpoucke
 */
class TrackingDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return TrackingDataManagerInterface The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_tracking_data_manager.class.php';
            $class = Utilities :: underscores_to_camelcase($type) . 'TrackingDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }
}
?>