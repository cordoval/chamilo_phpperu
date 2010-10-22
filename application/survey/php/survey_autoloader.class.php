<?php
namespace application\survey;

class SurveyAutoloader
{
	static function load($classname)
	{
		$list = array(
        'survey_publication_mail' => 'survey_publication_mail.class.php', 
        'survey_publication' =>'survey_publication.class.php', 
        'survey_publication_rel_reporting_template_registration' => 'survey_publication_rel_reporting_template_registration.class.php', 
        'survey_rights' => 'survey_rights.class.php',
        'survey_data_manager' => 'survey_data_manager.class.php',
        'survey_data_manager_interface' => 'survey_data_manager_interface.class.php',
        'survey_publication_rel_reporting_template_form' => 'publication_rel_reporting_template_form.class.php',
        'survey_subscribe_group_form' => 'subscribe_group_form.class.php',
		'survey_subscribe_user_form' => 'subscribe_user_form.class.php',
		'survey_publication_form' => 'survey_publication_form.class.php',
		'survey_publication_mailer_form' => 'survey_publication_mailer_form.class.php',
		
        'survey_manager' => 'survey_manager/survey_manager.class.php',
		'survey_manager_browser_component' => 'survey_manager/component/browser.class.php',
		'survey_manager_builder_component' => 'survey_manager/component/builder.class.php',
		'survey_manager_deleter_component' => 'survey_manager/component/deleter.class.php',
		'survey_manager_editor_component' => 'survey_manager/component/editor.class.php',
		'survey_manager_invitation_canceler_component' => 'survey_manager/component/invitation_canceler.class.php',
		'survey_manager_inviter_component' => 'survey_manager/component/inviter.class.php',
		'survey_manager_mailer_component' => 'survey_manager/component/mailer.class.php',
		'survey_manager_participant_browser_component' => 'survey_manager/component/participant_browser.class.php',
		'survey_manager_publisher_component' => 'survey_manager/component/publisher.class.php',
		'survey_manager_reporting_component' => 'survey_manager/component/reporting.class.php',
		'survey_manager_rights_editor_component' => 'survey_manager/component/rights_editor.class.php',
		'survey_manager_subscribe_group_component' => 'survey_manager/component/subscribe_group.class.php',
		'survey_manager_subscribe_user_component' => 'survey_manager/component/subscribe_user.class.php',
		'survey_manager_taker_component' => 'survey_manager/component/taker.class.php',
		
		'survey_export_manager' => 'export_manager/export_manager.class.php',
		'survey_export_manager_survey_excel_exporter_component' => 'survey_export_manager/component/survey_excel_exporter.class.php',
		'survey_export_manager_survey_excel_median_exporter_component' => 'survey_export_manager/component/survey_excel_exporter.class.php',
		'survey_export_manager_survey_excel_percentage_exporter_component' => 'survey_export_manager/component/survey_excel_exporter.class.php',
		'survey_export_manager_survey_spss_excel_exporter_component' => 'survey_export_manager/component/survey_excel_exporter.class.php',
		'survey_export_manager_survey_spss_syntax_exporter_component' => 'survey_export_manager/component/survey_excel_exporter.class.php',
		
		
		'survey_reporting_manager' => 'reporting_manager/reporting_manager.class.php',
        'survey_reporting_manager_browser_component' => 'reporting_manager/component/browser.class.php',
		'survey_reporting_manager_creator_component' => 'reporting_manager/component/creator.class.php',
		'survey_reporting_manager_deleter_component' => 'reporting_manager/component/deleter.class.php',
		'survey_reporting_manager_editor_component' => 'reporting_manager/component/editor.class.php',
		'survey_reporting_manager_reporting_component' => 'reporting_manager/component/reporting.class.php',
		'survey_reporting_manager_rights_editor_component' => 'reporting_manager/component/rights_editor.class.php',
		'survey_publication_rel_reporting_template_table' => 'reporting_manager/component/publication_rel_reporting_template_table/table.class.php',
		'survey_reporting_template_table' => 'reporting_manager/component/reporting_template_table/table.class.php');
				
		
        $lower_case = Utilities :: camelcase_to_underscores($classname);
        
        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once WebApplication :: get_application_class_lib_path('survey/php/lib') . $url;
            return true;
        }
        
        return false;
	}
}

?>