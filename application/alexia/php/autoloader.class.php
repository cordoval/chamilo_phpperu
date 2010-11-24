<?php
namespace application\alexia;

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
            'alexia_publication' => 'alexia_publication.class.php',
            'alexia_publication_group' => 'alexia_publication_group.class.php',
            'alexia_publication_user' => 'alexia_publication_user.class.php',
            'alexia_data_manager' => 'alexia_data_manager.class.php',
            'alexia_data_manager_interface' => 'alexia_data_manager_interface.class.php',
            'alexia_publication_form' => 'forms/alexia_publication_form.class.php',
            'alexia_manager' => 'alexia_manager/alexia_manager.class.php',
            'alexia_publisher' => 'publisher/alexia_publisher.class.php'
        );

        $lower_case = Utilities :: camelcase_to_underscores($classname);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('alexia') . $url;
            return true;
        }

        return false;
    }

}

?>