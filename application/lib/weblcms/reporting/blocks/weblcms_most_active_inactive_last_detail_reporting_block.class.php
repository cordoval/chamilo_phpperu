<?php

require_once dirname (__FILE__) . '/../weblcms_reporting_block.class.php';

class WeblcmsMostActiveInactiveLastDetailReportingBlock extends WeblcmsReportingBlock
{
	public function count_data()
	{
		require_once Path :: get_user_path() . 'trackers/visit_tracker.class.php';
        $wdm = WeblcmsDataManager :: get_instance();
        $tracker = new VisitTracker();
        $courses = $wdm->retrieve_courses(null, null, null, $params['order_by']);
        while ($course = $courses->next_result())
        {
            $lastaccess = Translation :: get('NeverAccessed');
            $lastpublication = Translation :: get('NothingPublished');

            $condition = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*&course=' . $course->get_id() . '*');
            $trackerdata = $tracker->retrieve_tracker_items($condition);
            foreach ($trackerdata as $key => $value)
            {
                $lastaccess = $value->get_leave_date();
            }

            $condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course->get_id());
            $publications = $datamanager->retrieve_content_object_publications_new($condition);

            while ($publication = $publications->next_result())
            {
                $lastpublication = $publication->get_modified_date();
                //$lastpublication = date_create($lastpublication);
                $lastpublication = date('Y-m-d G:i:s', $lastpublication);
            }

            $arr[Translation :: get('Course')] = '<a href="run.php?go=courseviewer&course=' . $course->get_id() . '&application=weblcms&" />' . $course->get_name() . '</a>';
            $arr[Translation :: get('LastVisit')] = $lastaccess;
            $arr[Translation :: get('LastPublication')] = $lastpublication;
        }

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
        //$modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        //$modes[ReportingFormatter::DISPLAY_TABLE] = Translation :: get('Table');
        //$modes[ReportingChartFormatter::DISPLAY_PIE] = Translation :: get('Chart:Pie');
        return $modes;
	}
}
?>