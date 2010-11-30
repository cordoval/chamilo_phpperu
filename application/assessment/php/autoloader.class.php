<?php

namespace application\assessment;

use common\libraries\Utilities;
use common\libraries\WebApplication;

/**
 * $Id: user_autoloader.class.php 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */
class Autoloader
{

    public static $class_name;

    static function load($classname)
    {
       self :: $class_name = $classname;
        $list = array(
            'assessment_manager' => 'assessment_manager/assessment_manager.class.php',
            'results_export' => 'assessment_manager/component/assessment_results_export_form/export.class.php',
            );
        $lower_case = Utilities :: camelcase_to_underscores(self :: $class_name);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('assessment') . $url;
            return true;
        }

        return false;
    }

}

?>