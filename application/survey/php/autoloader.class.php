<?php

namespace application\survey;

use common\libraries\Utilities;
use common\libraries\WebApplication;

class Autoloader
{

    static function load($classname)
    {

        $list = array(
            'survey_publication_mail' => 'survey_publication_mail.class.php',
            'survey_publication' => 'survey_publication.class.php',
            'survey_publication_rel_reporting_template_registration' => 'survey_publication_rel_reporting_template_registration.class.php',
            'survey_rights' => 'survey_rights.class.php',
            'survey_data_manager' => 'survey_data_manager.class.php',
            'survey_data_manager_interface' => 'survey_data_manager_interface.class.php',
            'survey_publication_rel_reporting_template_form' => 'forms/publication_rel_reporting_template_form.class.php',
            'survey_subscribe_group_form' => 'forms/subscribe_group_form.class.php',
            'survey_subscribe_user_form' => 'forms/subscribe_user_form.class.php',
            'survey_publication_form' => 'forms/survey_publication_form.class.php',
            'survey_publication_mailer_form' => 'forms/survey_publication_mailer_form.class.php',
         	'survey_publication_rel_reporting_template_registration_form' => 'forms/publication_rel_reporting_template_form.class.php',
            'survey_manager' => 'survey_manager/survey_manager.class.php',
            'survey_publication_browser_table' => 'survey_manager/component/publication_browser/publication_browser_table.class.php',
            'default_survey_publication_table_cell_renderer' => 'tables/publication_table/default_survey_publication_table_cell_renderer.class.php',
            'default_survey_publication_table_column_model' => 'tables/publication_table/default_survey_publication_table_column_model.class.php',
            'default_survey_reporting_template_table_column_model' => 'tables/reporting_template_table/default_reporting_template_table_column_model.class.php',
            'default_survey_reporting_template_table_cell_renderer' => 'tables/reporting_template_table/default_reporting_template_table_cell_renderer.class.php',
            'default_survey_publication_rel_reporting_template_table_column_model' => 'tables/publication_rel_reporting_template_table/default_publication_rel_reporting_template_table_column_model.class.php',
            'default_survey_publication_rel_reporting_template_table_cell_renderer' => 'tables/publication_rel_reporting_template_table/default_publication_rel_reporting_template_table_cell_renderer.class.php',
            'default_participant_table_column_model' => 'tables/participant_table/default_participant_table_column_model.class.php',
            'default_participant_table_cell_renderer' => 'tables/participant_table/default_participant_table_cell_renderer.class.php',
            'default_survey_user_table_column_model' => 'tables/user_table/default_user_table_column_model.class.php',
            'default_survey_user_table_cell_renderer' => 'tables/user_table/default_user_table_cell_renderer.class.php',
            'survey_export_manager' => 'export_manager/export_manager.class.php',
            'survey_reporting_block' => '../reporting/survey_reporting_block.class.php',
            'survey_level_reporting_template_interface' => '../reporting/survey_level_reporting_template_interface.class.php',
            'survey_participant_mail_tracker' => '../trackers/survey_participant_mail_tracker.class.php',
            'survey_participant_tracker' => '../trackers/survey_participant_tracker.class.php',
            'survey_publication_changes_tracker' => '../trackers/survey_publication_changes_tracker.class.php',
            'survey_question_answer_tracker' => '../trackers/survey_question_answer_tracker.class.php',
            'survey_reporting_manager' => 'reporting_manager/reporting_manager.class.php',
            'survey_publication_rel_reporting_template_table' => 'reporting_manager/component/publication_rel_reporting_template_table/table.class.php',
            'survey_reporting_template_table' => 'reporting_manager/component/reporting_template_table/table.class.php',
            'survey_participant_browser_table' => 'survey_manager/component/participant_browser/participant_browser_table.class.php',
         	'default_participant_table_cell_renderer' => 'tables/participant_table/default_participant_table_cell_renderer.class.php',
            'default_participant_table_column_model' => 'tables/participant_table/default_participant_table_column_model.class.php',
         	'survey_user_browser_table' => 'survey_manager/component/user_browser/user_browser_table.class.php',
         	'default_survey_user_table_cell_renderer' => 'tables/user_table/default_user_table_cell_renderer.class.php',
            'default_survey_user_table_column_model' => 'tables/user_table/default_user_table_column_model.class.php',
        	'survey_reporting_filter_wizard' => 'wizards/survey_reporting_filter_wizard.class.php');





        $lower_case = Utilities :: camelcase_to_underscores($classname);

        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('survey') . $url;
            return true;
        }

        return false;
    }

}

?>