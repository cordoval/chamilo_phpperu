<?php
require_once dirname (__FILE__) . '/../admin_reporting_block.class.php';
require_once PATH :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class AdminMostUsedWebApplicationsReportingBlock extends AdminReportingBlock
{
	public function count_data()
	{
		$reporting_data = new ReportingData();
		$reporting_data->set_rows(array(Translation :: get('numberVisit')));
	        
		require_once PATH :: get_user_path()  . 'trackers/visit_tracker.class.php';
		
		$applications = WebApplication::load_all(false);
		foreach($applications as $application){
			$condition = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*&application=' . $application . '*');
	        $tracker = new VisitTracker();
	        $trackerdata = $tracker->retrieve_tracker_items($condition);

	        $reporting_data->add_category($application);
	        $reporting_data->add_data_category_row($application, Translation :: get('number'), sizeof($trackerdata));
		}
        return $reporting_data;
	}	
	
	public function retrieve_data()
	{
		return $this->count_data();
	}
	
	public function get_application()
	{
		return AdminManager::APPLICATION_NAME;
	}
	
	public function get_available_displaymodes()
	{
		$modes = array();
        $modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        $modes[ReportingFormatter::DISPLAY_TABLE] = Translation :: get('Table');
        return $modes;
	}
}
?>