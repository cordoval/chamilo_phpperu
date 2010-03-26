<?php

require_once dirname (__FILE__) . '/../user_reporting_block.class.php';

class UserNoOfLoginsMonthReportingBlock extends UserReportingBlock
{
	public function count_data()
	{
		$reporting_data = new ReportingData();
		require_once (dirname(__FILE__) . '/../../trackers/login_logout_tracker.class.php');
        $condition = new EqualityCondition(LoginLogoutTracker :: PROPERTY_TYPE, 'login');
        $tracker = new LoginLogoutTracker();
        $trackerdata = $tracker->retrieve_tracker_items($condition);

		$months_names = array(Translation :: get('January'), Translation :: get('Februari'), Translation :: get('March'),Translation :: get('April'),Translation :: get('May'),Translation :: get('June'),Translation :: get('July'),Translation :: get('August'),Translation :: get('September'),Translation :: get('October'), Translation :: get('November'),Translation :: get('December'));
        $months = UserReportingBlock :: getDateArray($trackerdata, 'n');
        
		$reporting_data->set_categories($months_names);
        $reporting_data->set_rows(array(Translation :: get('logins')));
        
        foreach ($months_names as $key => $name)
        {
            $reporting_data->add_data_category_row($name, Translation :: get('logins'), ($months[$key+1] ? $months[$key+1] : 0));
        }
        return $reporting_data;
	}	
	
	public function retrieve_data()
	{
		return $this->count_data();		
	}
	
	function get_application()
	{
		return UserManager::APPLICATION_NAME;
	}
	
	public function get_available_displaymodes()
	{
		$modes = array();
        $modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        $modes[ReportingFormatter::DISPLAY_TABLE] = Translation :: get('Table');
        $modes[ReportingChartFormatter::DISPLAY_PIE] = Translation :: get('Chart:Pie');
        return $modes;
	}
}
?>
