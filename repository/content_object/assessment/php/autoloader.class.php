<?php

namespace repository\content_object\assessment;

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
        $list = array('assessment' => 'assessment.class.php',
            'assessment_builder' => 'builder/assessment_builder',
            'assessment_display' => 'display/assessment_display');
        $lower_case = Utilities :: camelcase_to_underscores($classname);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once $dirname(__FILE__) . '/' . $url;
            return true;
        }

        return false;
    }

}

?>