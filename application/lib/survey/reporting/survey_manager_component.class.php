<?php
class SurveyManagerComponent 
{
	public function SurveyManagerComponent()
	{
		
	}
	
	public function run()
	{
		$test = new SurveyAttemptReportingTemplate($this);
		$test->to_html();
	}	
}
?>