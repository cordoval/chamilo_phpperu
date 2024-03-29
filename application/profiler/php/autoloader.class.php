<?php

namespace application\profiler;

use common\libraries\WebApplication;
use common\libraries\Utilities;

/**
 * $Id: user_autoloader.class.php 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */
class Autoloader
{

    static function load($unqualified_class_name)
    {

        $list = array(
            'profiler_rights' => 'profiler_rights.class.php',
            'profiler_data_manager' => 'profiler_data_manager.class.php',
            'profiler_data_manager_interface' => 'profiler_data_manager_interface.class.php',
            'profiler_publication' => 'profiler_publication.class.php',
            'profiler_block.class.php' => 'profiler_block',
            'profiler_menu' => 'profiler_menu.class.php',
            'profile_publication_form' => 'profile_publication_form.class.php',
            'profiler_manager' => 'profiler_manager/profiler_manager.class.php',
            'profiler_category' => 'category_manager/profiler_category.class.php',
            'profiler_category_manager' => 'category_manager/profiler_category_manager.class.php',
            'profile_publisher' => 'publisher/profile_publisher.class.php');

        $lower_case = Utilities :: camelcase_to_underscores($unqualified_class_name);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('profiler') . $url;
            return true;
        }

        return false;
    }
    

}

?>