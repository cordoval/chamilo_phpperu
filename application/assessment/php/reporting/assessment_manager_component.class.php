<?php

namespace application\assessment;

class AssessmentManagerComponent 
{
	public function __construct()
	{
		
	}
	
	public function run()
	{
		$test = new AssessmentAttemptReportingTemplate($this);
		$test->to_html();
	}	
}
?>