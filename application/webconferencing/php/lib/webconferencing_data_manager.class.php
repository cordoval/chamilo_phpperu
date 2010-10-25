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
class WebconferencingDataManager
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
            require_once dirname(__FILE__) . '/data_manager/' . Utilities :: camelcase_to_underscores($type) . '_webconferencing_data_manager.class.php';
            $class = $type . 'WebconferencingDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }
}
?>