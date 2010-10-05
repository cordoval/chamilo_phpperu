<?php
/**
 * $Id: linker_data_manager.class.php 199 2009-11-13 12:23:04Z chellee $
 * @package application.lib.linker
 */
/**
 *	This is a skeleton for a data manager for the Linker Application.
 *	Data managers must extend this class and implement its abstract methods.
 *
 *	@author Sven Vanpoucke
 */
class LinkerDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function LinkerDataManager()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return LinkersDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_linker_data_manager.class.php';
            $class = $type . 'LinkerDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }
}
?>