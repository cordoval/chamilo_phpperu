<?php
require_once dirname ( __FILE__ ) . '/../period_reporting_block.class.php';
require_once dirname ( __FILE__ ) . '/../../internship_organizer_manager/internship_organizer_manager.class.php';
class InternshipOrganizerPeriodUserTypeCountReportingBlock extends InternshipOrganizerPeriodReportingBlock {
	
	public function count_data() {
		
		$period_id = $this->get_period_id();
 		$period = InternshipOrganizerDataManager::get_instance()->retrieve_period($period_id);
		
 		$coordinator = InternshipOrganizerUserType::COORDINATOR;
		$coordinator_count = count($period->get_user_ids($coordinator));
 		$coach = InternshipOrganizerUserType::COACH;
		$coach_count = count($period->get_user_ids($coach));
		$student = InternshipOrganizerUserType::STUDENT;
		$student_count = count($period->get_user_ids($student));
 		
		$reporting_data = new ReportingData();
		
		$reporting_data->set_categories ( array (Translation::get ( 'InternshipOrganizerCoordinator' ),Translation::get (  'InternshipOrganizerCoach' ), Translation::get ( 'InternshipOrganizerStudent' )) );
		$reporting_data->set_rows ( array (Translation::get ( 'Count' ) ) );
		
		$reporting_data->add_data_category_row (Translation::get ( 'InternshipOrganizerCoordinator' ), Translation::get ( 'Count' ), $coordinator_count );
		$reporting_data->add_data_category_row (Translation::get ( 'InternshipOrganizerCoach' ), Translation::get ( 'Count' ), $coach_count );
		$reporting_data->add_data_category_row (Translation::get ( 'InternshipOrganizerStudent' ), Translation::get ( 'Count' ), $student_count );
		
		
		return $reporting_data;
	}
	
	public function retrieve_data() {
		return $this->count_data ();
	}
	
	function get_application() {
		return InternshipOrganizerManager::APPLICATION_NAME;
	}
	
	public function get_available_displaymodes() {
		$modes = array ();
		$modes [ReportingFormatter::DISPLAY_TABLE] = Translation::get ( 'Table' );
		$modes [ReportingChartFormatter::DISPLAY_PIE] = Translation::get ( 'Chart:Pie' );
		$modes [ReportingChartFormatter::DISPLAY_BAR] = Translation::get ( 'Chart:Bar' );
		$modes [ReportingChartFormatter::DISPLAY_LINE] = Translation::get ( 'Chart:Line' );
		$modes [ReportingChartFormatter::DISPLAY_FILLED_CUBIC] = Translation::get ( 'Chart:FilledCubic' );
		return $modes;
	}
}
?>