<?php
require_once CoreApplication :: get_application_class_lib_path('reporting') . '/reporting_template.class.php';
require_once WebApplication :: get_application_class_path('internship_organizer') . 'reporting/blocks/internship_organizer_period_user_type_count_reporting_block.class.php';
require_once WebApplication :: get_application_class_path('internship_organizer') . 'reporting/blocks/internship_organizer_period_user_count_reporting_block.class.php';
require_once WebApplication :: get_application_class_path('internship_organizer') . 'reporting/blocks/internship_organizer_period_student_reporting_block.class.php';
require_once WebApplication :: get_application_class_path('internship_organizer') . 'reporting/blocks/internship_organizer_period_coordinator_reporting_block.class.php';
require_once WebApplication :: get_application_class_path('internship_organizer') . 'reporting/blocks/internship_organizer_period_coach_reporting_block.class.php';


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