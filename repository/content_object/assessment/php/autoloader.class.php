<?php

namespace repository\content_object\assessment;

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
        $list = array('assessment' => 'assessment', 'assessment_builder' => 'builder/assessment_builder',
                'assessment_complex_display_support' => 'display/assessment_complex_display_support',
                'assessment_complex_display_preview' => 'display/assessment_complex_display_preview',
                'assessment_display' => 'display/assessment_display',
                'question_display' => 'display/component/viewer/wizard/inc/question_display',
                'assessment_viewer_wizard_page' => 'display/component/viewer/wizard/assessment_viewer_wizard_page',
                'feedback_display_configuration' => 'display/feedback_display_configuration');
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