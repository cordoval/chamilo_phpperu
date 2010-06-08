<?php
require_once dirname (__FILE__) . '/../weblcms_course_reporting_block.class.php';
require_once PATH::get_reporting_path() . '/lib/reporting_data.class.php';

class WeblcmsMostActiveInactiveLastDetailReportingBlock extends WeblcmsCourseReportingBlock
{
	public function count_data()
	{
		$reporting_data = new ReportingData();
        $reporting_data->set_rows(array(Translation :: get('LastVisit'), Translation :: get('LastPublication')));
        
		require_once Path :: get_user_path() . 'trackers/visit_tracker.class.php';
        $wdm = WeblcmsDataManager :: get_instance();
        $tracker = new VisitTracker();
        $courses = $wdm->retrieve_courses();
        while ($course = $courses->next_result())
        {
            
        	$lastaccess = Translation :: get('NeverAccessed');
            $lastpublication = Translation :: get('NothingPublished');

            $condition = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*&course=' . $course->get_id() . '*');
            $trackerdata = $tracker->retrieve_tracker_items($condition);
            foreach ($trackerdata as $key => $value)
            {
                $lastaccess = DatetimeUtilities :: format_locale_date(null,$value->get_leave_date());
            }

            $condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course->get_id());
            $publications = $wdm->retrieve_content_object_publications_new($condition);

            while ($publication = $publications->next_result())
            {
                $lastpublication = DatetimeUtilities :: format_locale_date(null,$publication->get_modified_date());
                //$lastpublication = date('Y-m-d G:i:s', $lastpublication);
            }

            $reporting_data->add_category($course->get_name());
            //$reporting_data->add_data_category_row($course->get_name(), Translation :: get('Course'), '<a href="run.php?go=courseviewer&course=' . $course->get_id() . '&application=weblcms&" />' . $course->get_name() . '</a>');
			$reporting_data->add_data_category_row($course->get_name(), Translation :: get('LastVisit'), $lastaccess);
			$reporting_data->add_data_category_row($course->get_name(), Translation :: get('LastPublication'), $lastpublication);
        }
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
        //$modes[ReportingChartFormatter::DISPLAY_PIE] = Translation :: get('Chart:Pie');
        return $modes;
	}
}
?>