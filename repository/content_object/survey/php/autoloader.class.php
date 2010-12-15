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
            'survey_context_template' => 'survey_context_template',
            'survey_context' => 'survey_context',
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
            'survey_context_table' => 'manage/context/component/context_table/table',
            'survey_page_table' => 'manage/context/component/registration_browser/browser_table',
            'survey__table' => 'manage/context/component/registration_browser/browser_table',
            'survey_context_template_browser_table' => 'manage/context/component/registration_browser/browser_table',
            'survey_template_table' => 'manage/context/component/registration_browser/browser_table',
            'survey_viewer_form' => 'display/component/viewer/survey_viewer_form',
            'survey_display_survey_viewer_component' => 'display/component/survey_viewer',
            'survey_menu' => 'display/component/survey_menu',
        
            'survey_viewer_form' => 'display/component/viewer/survey_viewer_form',
            'survey_question_display' => 'display/component/viewer/inc/survey_question_display',
            'survey_context_manager' => 'manage/context/survey_context_manager',
            'default_survey_page_table_column_model' => 'manage/context/tables/page_table/default_page_table_column_model',
            'default_survey_page_table_cell_renderer' => 'manage/context/tables/page_table/default_page_table_cell_renderer',
            'survey_context_form' => 'manage/context/forms/context_form',
        	'import_context_form' => 'manage/context/forms/import_context_form',
        	'survey_context_manager_context_template_viewer_component' => 'manage/context/component/context_template_viewer',
        	'import_template_form' => 'manage/context/forms/import_template_form',
            'default_survey_context_table_column_model' => 'manage/context/tables/context_table/default_context_table_column_model',
            'default_survey_context_table_cell_renderer' => 'manage/context/tables/context_table/default_context_table_cell_renderer',
            'default_survey_context_template_table_column_model' => 'manage/context/tables/context_template_table/default_context_template_table_column_model',
            'default_survey_context_template_table_cell_renderer' => 'manage/context/tables/context_template_table/default_context_template_table_cell_renderer',
            'default_survey_table_column_model' => 'manage/context/tables/survey_table/default_survey_table_column_model',
            'default_survey_table_cell_renderer' => 'manage/context/tables/survey_table/default_survey_table_cell_renderer',
            'default_survey_template_table_column_model' => 'manage/context/tables/template_table/default_template_table_column_model',
            'default_survey_template_table_cell_renderer' => 'manage/context/tables/template_table/default_template_table_cell_renderer',
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