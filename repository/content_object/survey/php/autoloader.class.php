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
        $list = array(
            'survey' => 'survey',
            'survey_context_template' => 'survey_context_template.class.php',
            'survey_builder' => 'builder/survey_builder',
            'survey_display' => 'display/survey_display',
            'survey_analyzer' => 'analyzer/analyzer',
            'survey_context_registration_browser_table' => 'manage/context/component/registration_browser/browser_table',
            'survey_context_rel_group_table' => 'manage/context/component/registration_browser/browser_table',
            'survey_context_rel_user_browser_table' => 'manage/context/component/registration_browser/browser_table',
            'survey_context__table' => 'manage/context/component/registration_browser/browser_table',
            'survey_context_template_rel_page_table' => 'manage/context/component/registration_browser/browser_table',
            'survey_context_template_subscribe_page_browser_table' => 'manage/context/component/registration_browser/browser_table',
            'survey_context_template_browser_table' => 'manage/context/component/registration_browser/browser_table',
            'survey_page_table' => 'manage/context/component/registration_browser/browser_table',
            'survey__table' => 'manage/context/component/registration_browser/browser_table',
            'survey_context_template_browser_table' => 'manage/context/component/registration_browser/browser_table',
            'survey_template_table' => 'manage/context/component/registration_browser/browser_table',
            'survey_viewer_wizard_page' => 'display/component/viewer/browser_table/survey_viewer_wizard_page.class.php',
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