<?php
/**
 * $Id: rights_data_manager.class.php 157 2009-11-10 13:44:02Z vanpouckesven $
 * @package rights.lib
 * @author Hans de Bisschop
 *
 * This is a skeleton for a data manager for the Rights application.
 *
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class RightsDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return RightsDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_rights_data_manager.class.php';
            $class = Utilities :: underscores_to_camelcase($type) . 'RightsDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }
}
?>