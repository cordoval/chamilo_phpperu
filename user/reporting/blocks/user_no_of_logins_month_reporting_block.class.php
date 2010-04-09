<?php

require_once dirname (__FILE__) . '/../user_reporting_block.class.php';

class UserNoOfLoginsMonthReportingBlock extends UserReportingBlock
{
	public function count_data()
	{
		$reporting_data = new ReportingData();
		require_once (dirname(__FILE__) . '/../../trackers/login_logout_tracker.class.php');
        $conditions = array();
		$conditions[] = new EqualityCondition(LoginLogoutTracker :: PROPERTY_TYPE, 'login');
		$user_id = $this->get_user_id();
		if (isset($user_id))
		{
			$conditions[] = new EqualityCondition(LoginLogoutTracker::PROPERTY_USER_ID, $user_id); 
		}
		$condition = new AndCondition($conditions);

        $tracker = new LoginLogoutTracker();
        $trackerdata = $tracker->retrieve_tracker_items($condition);

		$months_names = array(Translation :: get('JanuaryLong'), Translation :: get('FebruaryLong'), Translation :: get('MarchLong'),Translation :: get('AprilLong'),Translation :: get('MayLong'),Translation :: get('JuneLong'),Translation :: get('JulyLong'),Translation :: get('AugustLong'),Translation :: get('SeptemberLong'),Translation :: get('OctoberLong'), Translation :: get('NovemberLong'),Translation :: get('DecemberLong'));
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
        //$modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        $modes[ReportingFormatter::DISPLAY_TABLE] = Translation :: get('Table');
        $modes[ReportingChartFormatter::DISPLAY_PIE] = Translation :: get('Chart:Pie');
        return $modes;
	}
}
?>