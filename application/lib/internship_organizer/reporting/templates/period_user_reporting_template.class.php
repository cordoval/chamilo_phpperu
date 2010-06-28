<?php
require_once PATH :: get_reporting_path() . '/lib/reporting_template.class.php';
require_once dirname(__FILE__) . '/../blocks/period_user_reporting_block.class.php';

class PeriodUserReportingTemplate extends ReportingTemplate
{
	function PeriodUserReportingTemplate($parent)
	{
		parent :: __construct($parent);
		$this->add_reporting_block(new PeriodUserReportingBlock($this));
	}
	
	public function display_context()
	{
  
	}
	
	function get_application()
	{
		return InternshipOrganizerManager::APPLICATION_NAME;
	}

}
?>