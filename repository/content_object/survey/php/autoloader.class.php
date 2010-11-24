<?php

namespace repository\content_object\survey;

use common\libraries\Utilities;

/**
 * $Id: user_autoloader 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */
class Autoloader
{

    static function load($classname)
    {
        $list = array('survey' => 'survey',
            'survey_builder' => 'builder/survey_builder',
            'survey_display' => 'display/survey_display');
        $lower_case = Utilities :: camelcase_to_underscores($classname);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once dirname(__FILE__) . '/' . $url . '.class.php';
            return true;
        }

        return false;
    }

}

?>