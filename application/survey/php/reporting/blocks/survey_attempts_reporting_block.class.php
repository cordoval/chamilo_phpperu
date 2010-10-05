<?php
require_once dirname (__FILE__) . '/../survey_reporting_block.class.php';
require_once dirname (__FILE__) . '/../../survey_manager/survey_manager.class.php';
class SurveyAttemptsReportingBlock extends SurveyReportingBlock
{
	public function count_data()
	{
		return 0;	
	}	
	
	public function retrieve_data()
	{
		return "test attempts";
	}
	
	function get_application()
	{
		return SurveyManager::APPLICATION_NAME;
	}
}
?>