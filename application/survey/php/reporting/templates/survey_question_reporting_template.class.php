<?php
require_once PATH :: get_reporting_path() . '/lib/reporting_template.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_question_reporting_block.class.php';

class SurveyQuestionReportingTemplate extends ReportingTemplate
{
	function SurveyQuestionReportingTemplate($parent)
	{
		parent :: __construct($parent);
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