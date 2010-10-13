<?php
namespace menu;

use common\libraries\Configuration;
use common\libraries\Utilities;

/**
 * $Id: menu_data_manager.class.php 157 2009-11-10 13:44:02Z vanpouckesven $
 * @package menu.lib
 */
/**
 *	This is a skeleton for a data manager for the Users table.
 *	Data managers must extend this class and implement its abstract methods.
 *
 *	@author Hans De Bisschop
 *	@author Dieter De Neef
 */
abstract class MenuDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return UserDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_menu_data_manager.class.php';
            $class = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($type) . 'MenuDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }
}
?>