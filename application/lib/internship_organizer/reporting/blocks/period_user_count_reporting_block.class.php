<?php
require_once dirname ( __FILE__ ) . '/../period_reporting_block.class.php';
require_once dirname ( __FILE__ ) . '/../../internship_organizer_manager/internship_organizer_manager.class.php';
class InternshipOrganizerPeriodUserCountReportingBlock extends InternshipOrganizerPeriodReportingBlock {
	
	public function count_data() {
		
		$period_id = $this->get_period_id();
 		$period = InternshipOrganizerDataManager::get_instance()->retrieve_period($period_id);
		
 		
 		$group_count = count($period->get_unique_group_ids());
 		$user_count = count($period->get_unique_user_ids());
 		
 		$coordinator = InternshipOrganizerUserType::COORDINATOR;
		$coach = InternshipOrganizerUserType::COACH;
		$student = InternshipOrganizerUserType::STUDENT;
		$all_types = array($coordinator, $coach, $student);
		$total_count = count($period->get_user_ids($all_types));
 		
		$reporting_data = new ReportingData();
		
		$reporting_data->set_categories ( array (Translation::get ( 'InternshipOrganizerGroup' ),Translation::get (  'InternshipOrganizerUser' ), Translation::get ( 'InternshipOrganizerTotal' )) );
		$reporting_data->set_rows ( array (Translation::get ( 'Count' ) ) );
		
		$reporting_data->add_data_category_row (Translation::get ( 'InternshipOrganizerGroup' ), Translation::get ( 'Count' ), $group_count );
		$reporting_data->add_data_category_row (Translation::get ( 'InternshipOrganizerUser' ), Translation::get ( 'Count' ), $user_count );
		$reporting_data->add_data_category_row (Translation::get ( 'InternshipOrganizerTotal' ), Translation::get ( 'Count' ), $total_count );
		
		
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