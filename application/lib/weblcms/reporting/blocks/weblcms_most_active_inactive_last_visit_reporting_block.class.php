<?php

require_once dirname (__FILE__) . '/../weblcms_reporting_block.class.php';

class WeblcmsMostActiveInactiveLastVisitReportingBlock extends WeblcmsReportingBlock
{
	public function count_data()
	{
		require_once Path :: get_user_path() . 'trackers/visit_tracker.class.php';
        $wdm = WeblcmsDataManager :: get_instance();
        $tracker = new VisitTracker();
        $courses = $wdm->retrieve_courses(null, null, null, $params['order_by']);

        $arr[Translation :: get('Past24hr')][0] = 0;
        $arr[Translation :: get('PastWeek')][0] = 0;
        $arr[Translation :: get('PastMonth')][0] = 0;
        $arr[Translation :: get('PastYear')][0] = 0;
        $arr[Translation :: get('NeverAccessed')][0] = 0;

        while ($course = $courses->next_result())
        {
            $lastaccess = 0;
            $condition = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*&course=' . $course->get_id() . '*');
            $trackerdata = $tracker->retrieve_tracker_items($condition);
            foreach ($trackerdata as $key => $value)
            {
                $lastaccess = $value->get_leave_date();
            }

            if ($lastaccess == 0)
            {
                $arr[Translation :: get('NeverAccessed')][0] ++;
            }
            else
                if (strtotime($lastaccess) > time() - 86400)
                {
                    $arr[Translation :: get('Past24hr')][0] ++;
                }
                else
                    if (strtotime($lastaccess) > time() - 604800)
                    {
                        $arr[Translation :: get('PastWeek')][0] ++;
                    }
                    else
                        if (strtotime($lastaccess) > time() - 18144000)
                        {
                            $arr[Translation :: get('PastMonth')][0] ++;
                        }
                        else
                            if (strtotime($lastaccess) > time() - 31536000)
                            {
                                $arr[Translation :: get('PastYear')][0] ++;
                            }
                            else
                            {
                                $arr[Translation :: get('MoreThenOneYear')][0] ++;
                            }
        }
        $description[0] = Translation :: get('Time');
        $description[1] = Translation :: get('TimesAccessed');
        return Reporting :: getSerieArray($arr, $description);
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
        return $modes;
	}
}
?>