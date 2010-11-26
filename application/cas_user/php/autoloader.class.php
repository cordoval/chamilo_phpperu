<?php

namespace application\cas_user;

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
            'cas_user_data_manager' => 'cas_user_data_manager.class.php',
            'cas_user_data_manager_interface' => 'cas_user_data_manager_interface.class.php',
            'cas_user_manager' => 'cas_user_manager/cas_user_manager.class.php',
            'cas_account_manager' => 'cas_account_manager/cas_account_manager.class.php'
        );

        $lower_case = Utilities :: camelcase_to_underscores($classname);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('cas_user') . $url;
            return true;
        }

        return false;
    }

}

?>