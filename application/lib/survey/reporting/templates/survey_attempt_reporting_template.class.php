<?php
require_once PATH :: get_reporting_path() . '/lib/reporting_template.class.php';

class SurveyAttemptReportingTemplate extends ReportingTemplate
{
	function SurveyAttemptsReportingTemplate($parent)
	{
		parent :: $parent;
		$this->add_reporting_block(new SurveyAttemptsReportingBlock());
	}
	
	public function display_context()
	{
		return null;	
	}
}
?>