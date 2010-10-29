<?php

namespace repository\content_object\learning_path;

use common\libraries\Utilities;

/**
 * $Id: user_autoloader.class.php 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */
class Autoloader
{

    static function load($classname)
    {
        $list = array('learning_path' => 'learning_path.class.php',
            'learning_path_builder' => 'builder/learning_path_builder.class.php',
            'learning_path_display' => 'display/learning_path_display.class.php');
        $lower_case = Utilities :: camelcase_to_underscores($classname);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once dirname(__FILE__) . '/' . $url;
            return true;
        }

        return false;
    }

}

?>