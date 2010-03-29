<?php

class AssessmentManagerComponent 
{
	public function AssessmentManagerComponent()
	{
		
	}
	
	public function run()
	{
		$test = new AssessmentAttemptReportingTemplate($this);
		$test->to_html();
	}	
}
?>