<?php

namespace application\profiler;

use common\libraries\Configuration;
use common\libraries\WebApplication;
use common\libraries\Utilities;
/**
 * $Id: profiler_data_manager.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler
 */
class ProfilerDataManager
{

    private static $instance;

    protected function ProfilerDataManager()
    {
        $this->initialize();
    }

    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once WebApplication :: get_application_class_lib_path('profiler') . 'data_manager/' . strtolower($type) . '_profiler_data_manager.class.php';
            $class = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($type) . 'ProfilerDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }
}
?>