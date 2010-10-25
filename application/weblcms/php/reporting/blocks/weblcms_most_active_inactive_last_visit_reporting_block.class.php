<?php
require_once dirname (__FILE__) . '/../weblcms_course_reporting_block.class.php';
require_once PATH::get_reporting_path() . '/lib/reporting_data.class.php';

class WeblcmsMostActiveInactiveLastVisitReportingBlock extends WeblcmsCourseReportingBlock
{
	public function count_data()
	{
		$reporting_data = new ReportingData();
		require_once Path :: get_user_path() . 'trackers/visit_tracker.class.php';
        $wdm = WeblcmsDataManager :: get_instance();
        $tracker = new VisitTracker();
        $courses = $wdm->retrieve_courses();

        $arr[Translation :: get('Past24hr')] = 0;
        $arr[Translation :: get('PastWeek')] = 0;
        $arr[Translation :: get('PastMonth')] = 0;
        $arr[Translation :: get('PastYear')] = 0;
        $arr[Translation :: get('NeverAccessed')] = 0;
        $arr[Translation :: get('MoreThenOneYear')] = 0;

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
                $arr[Translation :: get('NeverAccessed')] ++;
            }
            else
                if (strtotime($lastaccess) > time() - 86400)
                {
                    $arr[Translation :: get('Past24hr')] ++;
                }
                else
                    if (strtotime($lastaccess) > time() - 604800)
                    {
                        $arr[Translation :: get('PastWeek')] ++;
                    }
                    else
                        if (strtotime($lastaccess) > time() - 18144000)
                        {
                            $arr[Translation :: get('PastMonth')] ++;
                        }
                        else
                            if (strtotime($lastaccess) > time() - 31536000)
                            {
                                $arr[Translation :: get('PastYear')] ++;
                            }
                            else
	                            {
	                                $arr[Translation :: get('MoreThenOneYear')] ++;
	                            }
        }
        
        $reporting_data->set_categories(array(Translation :: get('Past24hr'), Translation :: get('PastWeek'), Translation :: get('PastMonth'), Translation :: get('PastYear'),Translation :: get('MoreThenOneYear'), Translation :: get('NeverAccessed')));
        $reporting_data->set_rows(array(Translation :: get('TimesAccessed')));
		
		$reporting_data->add_data_category_row(Translation :: get('Past24hr'), Translation :: get('TimesAccessed'), $arr[Translation :: get('Past24hr')]);
		$reporting_data->add_data_category_row(Translation :: get('PastWeek'), Translation :: get('TimesAccessed'), $arr[Translation :: get('PastWeek')]);
		$reporting_data->add_data_category_row(Translation :: get('PastMonth'), Translation :: get('TimesAccessed'), $arr[Translation :: get('PastMonth')]);
		$reporting_data->add_data_category_row(Translation :: get('PastYear'), Translation :: get('TimesAccessed'), $arr[Translation :: get('PastYear')]);
		$reporting_data->add_data_category_row(Translation :: get('MoreThenOneYear'), Translation :: get('TimesAccessed'), $arr[Translation :: get('MoreThenOneYear')]);
		$reporting_data->add_data_category_row(Translation :: get('NeverAccessed'), Translation :: get('TimesAccessed'), $arr[Translation :: get('NeverAccessed')]);
	    
        return $reporting_data;
	}	
	
	public function retrieve_data()
	{
		return $this->count_data();		
	}
	
	function get_application()
	{
		return WeblcmsManager::APPLICATION_NAME;
	}
	
	public function get_available_displaymodes()
	{
		$modes = array();
        //$modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        $modes[ReportingFormatter::DISPLAY_TABLE] = Translation :: get('Table');
        return $modes;
	}
}
?>