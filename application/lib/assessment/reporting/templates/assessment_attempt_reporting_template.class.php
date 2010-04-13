<?php

require_once PATH :: get_reporting_path() . '/lib/reporting_template.clas.php';

class AssessmentAttemptReportingTemplate extends ReportingTemplate
{
	function AssessmentAttemptReportingTemplate($parent)
	{
		super::$parent;
		$this->add_reporting_block(new AssessmentAttemptsReportingBlock());
	} 
	
	function display_context()
	{
		//publicatie, content_object, application ... 
	}
/**
	 * 
	 */
	function get_application() {
		
	}

}
?>