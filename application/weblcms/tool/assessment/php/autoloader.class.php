<?php

namespace application\weblcms\tool\assessment;


use common\libraries\Utilities;

/**
 * $Id$
 * @author systho
 */
class Autoloader
{

    static function load($classname)
    {
        
        $list = array(
            'survey_publisher_component' => 'component/assessment_survey_publisher/survey_publisher_component.class.php',
        );

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