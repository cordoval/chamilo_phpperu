<?php
/**
 * $Id: alexia_data_manager.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.alexia
 */
class AlexiaDataManager
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
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_alexia_data_manager.class.php';
            $class = Utilities :: underscores_to_camelcase($type) . 'AlexiaDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

}
?>