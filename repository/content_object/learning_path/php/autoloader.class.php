<?php
namespace repository\content_object\learning_path;

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
        $list = array(
                'learning_path' => 'learning_path',
                'learning_path_builder' => 'builder/learning_path_builder',
                'learning_path_complex_display_support' => 'display/learning_path_complex_display_support',
                'learning_path_complex_display_preview' => 'display/learning_path_complex_display_preview',
                'learning_path_display' => 'display/learning_path_display',
                'dummy_lp_attempt_tracker' => 'display/preview/dummy_lp_attempt_tracker',
                'dummy_lpi_attempt_tracker' => 'display/preview/dummy_lpi_attempt_tracker',
                'learning_path_content_object_display' => 'display/learning_path_content_object_display');
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