<?php

require_once PATH :: get_reporting_path() . '/lib/reporting_template.clas.php';

class AssessmentAttemptReportingTemplate extends ReportingTemplate
{
	function AssessmentAttemptReportingTemplate($parent)
	{
		//super::$parent;
		//$this->add_reporting_block(new AssessmentAttemptsReportingBlock());
		
		$this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("AssessmentInformation"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
       
        parent :: __construct($parent, $id, $params);
	} 
	
	function display_context()
	{
		//publicatie, content_object, application ... 
		$this->get_properties();
		$this->get_registration_id();	
	}
}
?>