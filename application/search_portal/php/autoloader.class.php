<?php

namespace application\search_portal;

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
            'search_source' => 'search_source/search_source.class.php',
            'search_portal_manager' => 'search_portal_manager/search_portal_manager.class.php',
            'search_portal_block' => '../blocks/search_portal_block.class.php',
        	'SearchPortalBasic' => '../blocks/type/basic.class.php'
            );

        $lower_case = Utilities :: camelcase_to_underscores($classname);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('search_portal') . $url;
            return true;
        }
    }

}

?>