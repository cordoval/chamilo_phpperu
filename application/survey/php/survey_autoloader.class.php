<?php
namespace application\survey;

use common\libraries\Utilities;
use common\libraries\WebApplication;

class SurveyAutoloader
{
	static function load($classname)
	{
				
		$classname_parts = explode('\\', $classname);

        if (count($classname_parts) == 1)
        {
            return false;
        }
        else
        {
            $classname = $classname_parts[count($classname_parts) - 1];
            array_pop($classname_parts);
            if (implode('\\', $classname_parts) != __NAMESPACE__)
            {
                return false;
            }
        }
		
		
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
		
		'survey_export_manager' => 'export_manager/export_manager.class.php',
		
		'survey_reporting_block' => '../reporting/survey_reporting_block.class.php',
		
		'survey_participant_mail_tracker' => '../trackers/survey_participant_mail_tracker.class.php',
		'survey_participant_tracker' => '../trackers/survey_participant_tracker.class.php',
		'survey_publication_changes_tracker' => '../trackers/survey_publication_changes_tracker.class.php',
		'survey_question_answer_tracker' => '../trackers/survey_question_answer_tracker.class.php',
		
		
		'survey_reporting_manager' => 'reporting_manager/reporting_manager.class.php',
    	'survey_publication_rel_reporting_template_table' => 'reporting_manager/component/publication_rel_reporting_template_table/table.class.php',
		'survey_reporting_template_table' => 'reporting_manager/component/reporting_template_table/table.class.php');

		
		
		
		
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