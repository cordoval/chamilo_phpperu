<?php

namespace application\package;

use common\libraries\Configuration;
use common\libraries\WebApplication;
use common\libraries\Utilities;
/**
 * This is a skeleton for a data manager for the Package Application.
 * Data managers must extend this class and implement its abstract methods.
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

class PackageDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return PackageDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once WebApplication :: get_application_class_lib_path('package') . 'data_manager/' . strtolower($type) . '_package_data_manager.class.php';
            $class = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($type) . 'PackageDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }
}
?>