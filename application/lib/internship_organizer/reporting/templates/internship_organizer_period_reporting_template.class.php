<?php
require_once PATH :: get_reporting_path() . '/lib/reporting_template.class.php';
require_once dirname(__FILE__) . '/../blocks/period_user_type_count_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/period_user_count_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/period_student_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/period_coordinator_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/period_coach_reporting_block.class.php';


class InternshipOrganizerPeriodReportingTemplate extends ReportingTemplate
{
	function InternshipOrganizerPeriodReportingTemplate($parent)
	{
		parent :: __construct($parent);
		$this->add_reporting_block(new InternshipOrganizerPeriodUserTypeCountReportingBlock($this));
		$this->add_reporting_block(new InternshipOrganizerPeriodUserCountReportingBlock($this));
		$this->add_reporting_block(new InternshipOrganizerPeriodStudentReportingBlock($this));
		$this->add_reporting_block(new InternshipOrganizerPeriodCoordinatorReportingBlock($this));
		$this->add_reporting_block(new InternshipOrganizerPeriodCoachReportingBlock($this));
		
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