<?php
require_once PATH::get_reporting_path() . '/lib/reporting_data.class.php';
require_once dirname (__FILE__) . '/../weblcms_tool_reporting_block.class.php';

class WeblcmsPublicationDetailReportingBlock extends WeblcmsToolReportingBlock
{
	public function count_data()
	{
		$reporting_data = new ReportingData();
		
		require_once Path :: get_user_path() . 'trackers/visit_tracker.class.php';

		$course_id = $this->get_course_id();
		$user_id = $this->get_user_id();
		$tool = $this->get_tool();
		$pid = $this->get_pid();

        $tracker = new VisitTracker();
        $wdm = WeblcmsDataManager :: get_instance();
        $condition = new EqualityCondition(WeblcmsManager :: PARAM_TOOL, $tool);
        $lop = $wdm->retrieve_content_object_publication($pid);
        
        if (empty($lop))
        {
            $lop = RepositoryDataManager :: get_instance()->retrieve_content_object($pid);
            $title = $lop->get_title();
            $id = $lop->get_id();
            $descr = $lop->get_description();
        }
        else
        {
            $title = $lop->get_content_object()->get_title();
            $id = $pid;
            $descr = $lop->get_content_object()->get_description();
        }

        $condition = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*' . Tool::PARAM_PUBLICATION_ID . '=' . $pid . '*');
        $trackerdata = $tracker->retrieve_tracker_items($condition);

        foreach ($trackerdata as $key => $value)
        {
            if ($value->get_leave_date() > $lastaccess)
                $lastaccess = $value->get_leave_date();
        }
        $url = 'run.php?go=courseviewer&course=' . $course_id . '&tool=' . $tool . '&application=weblcms&' . Tool::PARAM_PUBLICATION_ID . '=' . $id . '&tool_action=view';
        
        /*$arr[Translation :: get('Title')][] = '<a href="' . $url . '">' . $title . '</a>';
        $arr[Translation :: get('Description')][] = Utilities :: truncate_string($descr, 50);
        $arr[Translation :: get('LastAccess')][] = $lastaccess;
        $arr[Translation :: get('TotalTimesAccessed')][] = count($trackerdata);*/

		$reporting_data->set_categories(array(Translation :: get('Title'), Translation :: get('Description'), Translation :: get('LastAccess'), Translation :: get('TotalTimesAccessed')));
        $reporting_data->set_rows(array(Translation :: get('count')));

        $reporting_data->add_data_category_row(Translation :: get('Title'), Translation :: get('count'), '<a href="' . $url . '">' . $title . '</a>');
        $reporting_data->add_data_category_row(Translation :: get('Description'), Translation :: get('count'), Utilities :: truncate_string($descr, 50));
        $reporting_data->add_data_category_row(Translation :: get('LastAccess'), Translation :: get('count'), $lastaccess);
        $reporting_data->add_data_category_row(Translation :: get('TotalTimesAccessed'), Translation :: get('count'), count($trackerdata));
        
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