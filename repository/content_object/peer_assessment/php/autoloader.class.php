<?php

namespace repository\content_object\peer_assessment;

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
            'peer_assessment' => 'peer_assessment',
            'peer_assessment_builder' => 'builder/peer_assessment_builder',
            'peer_assessment_display' => 'display/peer_assessment_display',
            'peer_assessment_result_viewer' => 'display/component/result_viewer/peer_assessment_result_viewer',
            'peer_assessment_result_viewer_wizard_page' => 'display/component/result_viewer/wizard/peer_assessment_result_viewer_wizard_page',
            'peer_assessment_viewer_wizard' => 'display/component/viewer/peer_assessment_viewer_wizard',
            'peer_assessment_viewer_wizard_page' => 'display/component/viewer/wizard/peer_assessment_viewer_wizard_page',
        );
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