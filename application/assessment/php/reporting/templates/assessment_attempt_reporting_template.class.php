<?php

namespace application\assessment;

use common\libraries\Path;
use reporting\ReportingTemplate;

require_once Path :: get_reporting_path() . '/lib/reporting_template.class.php';

class AssessmentAttemptReportingTemplate extends ReportingTemplate
{
	function __construct($parent)
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