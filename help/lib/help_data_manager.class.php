<?php
/**
 * $Id: help_data_manager.class.php 226 2009-11-13 14:44:03Z chellee $
 * @package help.lib
 */
class HelpDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return GroupsDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_help_data_manager.class.php';
            $class = Utilities :: underscores_to_camelcase($type) . 'HelpDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }
}
?>