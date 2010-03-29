<?php
require_once dirname (__FILE__) . '/../survey_reporting_block.class.php';
class SurveyQuestionReportingBlock extends SurveyReportingBlock
{
	public function count_data()
	{
		return 0;	
	}	
	
	public function retrieve_data()
	{
		return "test question";
	}
	
	function get_application()
	{
		return SurveyManager::APPLICATION_NAME;
	}
}
?>