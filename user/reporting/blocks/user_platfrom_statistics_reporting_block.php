<?php
require_once dirname (__FILE__) . '/../user_reporting_block.class.php';

class UserPlatformStatisticsReportingBlock extends UserReportingBlock
{
	public function count_data()
	{
		$uid = $params[ReportingManager :: PARAM_USER_ID];
        require_once (dirname(__FILE__) . '/../trackers/login_logout_tracker.class.php');
        require_once (dirname(__FILE__) . '/../trackers/visit_tracker.class.php');
        $conditions[] = new EqualityCondition(LoginLogoutTracker :: PROPERTY_USER_ID, $uid);
        $conditions[] = new EqualityCondition(LoginLogoutTracker :: PROPERTY_TYPE, 'login');
        $condition = new AndCondition($conditions);
        $tracker = new LoginLogoutTracker();
        $trackerdata = $tracker->retrieve_tracker_items($condition);
        foreach ($trackerdata as $key => $value)
        {
            if (! $firstconnection)
            {
                $firstconnection = $value->get_date();
                $lastconnection = $value->get_date();
            }
            if (! self :: greaterDate($value->get_date(), $firstconnection))
            {
                $firstconnection = $value->get_date();
            }
            else
                if (self :: greaterDate($value->get_date(), $lastconnection))
                {
                    $lastconnection = $value->get_date();
                }
        }
        $arr[Translation :: get('FirstConnection')][] = $firstconnection;
        $arr[Translation :: get('LastConnection')][] = $lastconnection;
        unset($conditions);
        unset($condition);
        $tracker = new VisitTracker();

        $condition = new EqualityCondition(VisitTracker :: PROPERTY_USER_ID, $uid);
        $trackerdata = $tracker->retrieve_tracker_items($condition);
        $arr[Translation :: get('TimeOnPlatform')][] = self :: get_total_time($trackerdata);

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
	
/**
     * Checks if a given start date is greater than a given end date
     * @param <type> $start_date
     * @param <type> $end_date
     * @return <type>
     */
    public static function greaterDate($start_date, $end_date)
    {
        $start = strtotime($start_date);
        $end = strtotime($end_date);
        if ($start - $end > 0)
            return 1;
        else
            return 0;
    }
}
?>