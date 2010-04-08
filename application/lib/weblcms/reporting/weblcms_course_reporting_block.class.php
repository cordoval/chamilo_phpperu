<?php
require_once dirname(__FILE__) . '/weblcms_reporting_block.class.php';

abstract class WeblcmsCourseReportingBlock extends WeblcmsReportingBlock
{
	private $course_id;

	function get_course_id()
	{
		return $this->course_id;
	}
	
	function set_course_id($course_id)
	{
		$this->course_id = $course_id;
	}
	
    static function get_total_time($trackerdata)
    {
        foreach ($trackerdata as $key => $value)
        {
            $time += strtotime($value->get_leave_date()) - strtotime($value->get_enter_date());
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
            $order_by = new ObjectTableOrder(VisitTracker :: PROPERTY_ENTER_DATE, SORT_DESC);
        $trackerdata = $tracker->retrieve_tracker_items_result_set($condition, $order_by);

        while ($visittracker = $trackerdata->next_result())
        {
            if (! $user)
            {
                $user = $udm->retrieve_user($visittracker->get_user_id());
            }

            $arr[Translation :: get('User')][] = $user->get_fullname();
            $arr[Translation :: get('LastAccess')][] = $visittracker->get_enter_date();
            $time = strtotime($visittracker->get_leave_date()) - strtotime($visittracker->get_enter_date());
            $time = mktime(0, 0, $time, 0, 0, 0);
            $time = date('G:i:s', $time);
            $arr[Translation :: get('TotalTime')][] = $time;
        }

        return $arr;
    } //visit_tra
}
?>