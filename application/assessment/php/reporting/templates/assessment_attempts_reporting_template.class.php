<?php

require_once PATH :: get_reporting_path() . 'lib/reporting_template.class.php';

class AssessmentAttemptsReportingTemplate extends ReportingTemplate
{
	function AssessmentAttemptsReportingTemplate($parent)
	{
		super::$parent;
		$this->add_reporting_block(new AssessmentAttemptsReportingBlock());
	} 
	
	function display_context()
	{
		//publicatie, content_object, application ... 
	}
	
    function get_application()
    {
    	return AssessmentManager::APPLICATION_NAME;
    }
}
?>