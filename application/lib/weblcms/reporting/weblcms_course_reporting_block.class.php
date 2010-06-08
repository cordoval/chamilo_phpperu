<?php
require_once dirname(__FILE__) . '/weblcms_reporting_block.class.php';

abstract class WeblcmsCourseReportingBlock extends WeblcmsReportingBlock
{
	function get_course_id()
	{
		return $this->get_parent()->get_parameter(WeblcmsManager::PARAM_COURSE);
	}

    static function get_total_time($trackerdata)
    {
        foreach ($trackerdata as $key => $value)
        {
            $time += $value->get_leave_date() - $value->get_enter_date();
        }

        $time = mktime(0, 0, $time, 0, 0, 0);
        $time = date('G:i:s', $time);
        return $time;
    }

	public static function visit_tracker_to_array($condition, $user, $order_by)
    {
        require_once Path :: get_user_path() . 'trackers/visit_tracker.class.php';
        $tracker = new VisitTracker();
        $udm = UserDataManager :: get_instance();

        if (! $order_by)
        {
            $order_by = new ObjectTableOrder(VisitTracker :: PROPERTY_ENTER_DATE, SORT_DESC);
        }
        $trackerdata = $tracker->retrieve_tracker_items_result_set($condition, null, null, $order_by);

        while ($visittracker = $trackerdata->next_result())
        {
            if (! $user)
            {
                $user = $udm->retrieve_user($visittracker->get_user_id());
            }

            $arr[Translation :: get('User')][] = $user->get_fullname();
            $arr[Translation :: get('LastAccess')][] = DatetimeUtilities :: format_locale_date(null, $visittracker->get_enter_date());
            $time = $visittracker->get_leave_date() - $visittracker->get_enter_date();
            $time = mktime(0, 0, $time, 0, 0, 0);
            $time = date('G:i:s', $time);
            $arr[Translation :: get('TotalTime')][] = $time;
        }

        return $arr;
    } //visit_tra
}
?>