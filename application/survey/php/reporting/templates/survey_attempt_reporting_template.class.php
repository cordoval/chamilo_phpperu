<?php
require_once PATH :: get_reporting_path() . '/lib/reporting_template.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_attempts_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_question_reporting_block.class.php';
//require_once dirname(__FILE__) . '/application/lib/survey/survey_publication_category_menu.class.php';

class SurveyAttemptReportingTemplate extends ReportingTemplate
{
	function SurveyAttemptReportingTemplate($parent)
	{
		parent :: __construct($parent);
		$this->add_reporting_block(new SurveyAttemptsReportingBlock($this));
		$this->add_reporting_block(new SurveyQuestionReportingBlock($this));		
	}
	
	public function display_context()
	{
  
	}
	
	function get_application()
	{
		return SurveyManager::APPLICATION_NAME;
	}

}
?>