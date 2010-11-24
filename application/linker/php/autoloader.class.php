<?php

namespace application\linker;

use common\libraries\Utilities;
use common\libraries\WebApplication;

/**
 * $Id: user_autoloader.class.php 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */
class Autoloader
{

    static function load($classname)
    {

        $list = array(
            'linker_data_manager' => 'linker_data_manager.class.php',
            'linker_data_manager_interface' => 'linker_data_manager_interface.class.php',
            'linker' => 'linker.class.php',
            'linker_manager' => 'linker_manager/linker_manager.class.php',
            'link_form' => 'forms/link_form.class.php'
        );

        $lower_case = Utilities :: camelcase_to_underscores($classname);
        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('linker') . $url;
            return true;
        }

        return false;
    }

}

?>