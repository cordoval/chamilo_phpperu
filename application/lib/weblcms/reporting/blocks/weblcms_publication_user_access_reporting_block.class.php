<?php
require_once PATH::get_reporting_path() . '/lib/reporting_data.class.php';
require_once dirname (__FILE__) . '/../weblcms_tool_reporting_block.class.php';

class WeblcmsPublicationUserAccessReportingBlock extends WeblcmsToolReportingBlock
{
	public function count_data()
	{
		require_once Path :: get_user_path() . 'trackers/visit_tracker.class.php';

		$reporting_data = new ReportingData();
        $tracker = new VisitTracker();
        
        $course_id = $this->get_course_id();
		$user_id = $this->get_user_id();
		$tool = $this->get_tool();
		$pid = $this->get_pid();

        $udm = UserDataManager :: get_instance();
        $user = $udm->retrieve_user($user_id);
		
        $conditions = array();
        $conditions[] = new EqualityCondition(VisitTracker::PROPERTY_USER_ID, $user_id); 
        $conditions[] = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*' . Tool::PARAM_PUBLICATION_ID . '=' . $pid . '*');
        $condition = new AndCondition($conditions);

        $order_by = new ObjectTableOrder(VisitTracker :: PROPERTY_ENTER_DATE, SORT_DESC);
        if ($params['order_by'])
            $order_by = $params['order_by'];

        $trackerdata = $tracker->retrieve_tracker_items_result_set($condition, $order_by);

        $reporting_data->set_categories(array(Translation :: get('User'), Translation :: get('LastAccess'), Translation :: get('TotalTime'), Translation :: get('Clicks')));
        $reporting_data->set_rows(array(Translation :: get('count')));
        
        $arr[Translation :: get('User')] = $udm->retrieve_user($user_id)->get_fullname();
        while ($value = $trackerdata->next_result())
        {
            $time = strtotime($value->get_leave_date()) - strtotime($value->get_enter_date());

            if ($value->get_enter_date() > $arr[Translation :: get('LastAccess')])
            {
            	$arr[Translation :: get('LastAccess')] = $value->get_enter_date();
            }
            $arr[Translation :: get('TotalTime')] += $time;
            $arr[Translation :: get('Clicks')] ++;
        }

        $reporting_data->add_data_category_row(Translation :: get('User'), Translation :: get('count'), $arr[Translation :: get('User')]);
        $reporting_data->add_data_category_row(Translation :: get('LastAccess'), Translation :: get('count'), $arr[Translation :: get('LastAccess')]);
        $reporting_data->add_data_category_row(Translation :: get('TotalTime'), Translation :: get('count'),  $arr[Translation :: get('TotalTime')]);
        $reporting_data->add_data_category_row(Translation :: get('Clicks'), Translation :: get('count'), $arr[Translation :: get('Clicks')]);
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
        $modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        $modes[ReportingFormatter::DISPLAY_TABLE] = Translation :: get('Table');
        return $modes;
	}
}
?>