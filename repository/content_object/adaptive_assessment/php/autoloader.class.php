<?php
namespace repository\content_object\adaptive_assessment;

use common\libraries\Utilities;

/**
 * @author Hans De Bisschop
 */
class Autoloader
{

    static function load($classname)
    {
        $list = array(
                'adaptive_assessment' => 'adaptive_assessment',
                'adaptive_assessment_builder' => 'builder/adaptive_assessment_builder',
                'adaptive_assessment_complex_display_support' => 'display/adaptive_assessment_complex_display_support',
                'adaptive_assessment_complex_display_preview' => 'display/adaptive_assessment_complex_display_preview',
                'adaptive_assessment_display' => 'display/adaptive_assessment_display',
                'dummy_lp_attempt_tracker' => 'display/preview/dummy_lp_attempt_tracker',
                'dummy_lpi_attempt_tracker' => 'display/preview/dummy_lpi_attempt_tracker',
                'adaptive_assessment_content_object_display' => 'display/adaptive_assessment_content_object_display');
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