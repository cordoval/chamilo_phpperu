<?php
use common\libraries\Translation;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;

require_once dirname (__FILE__) . '/../user_reporting_block.class.php';

class UserPlatformStatisticsReportingBlock extends UserReportingBlock
{
	public function count_data()
	{
		$uid = $params[ReportingManager :: PARAM_USER_ID];
        require_once (dirname(__FILE__) . '/../../trackers/login_logout_tracker.class.php');
        require_once (dirname(__FILE__) . '/../../trackers/visit_tracker.class.php');
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
		return $this->count_data();
	}

	function get_application()
	{
		return UserManager::APPLICATION_NAME;
	}

	public function get_available_displaymodes()
	{
		$modes = array();
        $modes["Text"] = Translation :: get('Text', null, Utilities :: COMMON_LIBRARIES);
        $modes["Table"] = Translation :: get('Table', null, 'reporting');
        $modes["Chart:Pie"] = Translation :: get('Chart:Pie', null, 'reporting');
        $modes["Chart:Bar"] = Translation :: get('Chart:Bar', null, 'reporting');
        $modes["Chart:Line"] = Translation :: get('Chart:Line', null, 'reporting');
        $modes["Chart:FilledCubic"] = Translation :: get('Chart:FilledCubic', null, 'reporting');
        return $modes;
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