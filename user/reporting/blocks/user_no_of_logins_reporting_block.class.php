<?php
require_once dirname (__FILE__) . '/../user_reporting_block.class.php';

class UserNoOfLoginsReportingBlock extends UserReportingBlock
{
	public function count_data()
	{

        require_once (dirname(__FILE__) . '/../trackers/login_logout_tracker.class.php');
        $condition = new EqualityCondition(LoginLogoutTracker :: PROPERTY_TYPE, 'login');
        $tracker = new LoginLogoutTracker();
        $trackerdata = $tracker->retrieve_tracker_items($condition);

        $arr[Translation :: get('Logins')][] = sizeof($trackerdata);

        return Reporting :: getSerieArray($arr);
    }	
	
	public function retrieve_data()
	{
		return count_data();		
	}
	
	function get_application()
	{
		return UserManager::APPLICATION_NAME;
	}
}
?>