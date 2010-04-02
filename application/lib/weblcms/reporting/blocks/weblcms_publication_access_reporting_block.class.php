<?php
require_once PATH::get_reporting_path() . '/lib/reporting_data.class.php';
require_once dirname (__FILE__) . '/../weblcms_tool_reporting_block.class.php';

class WeblcmsPublicationAccessReportingBlock extends WeblcmsToolReportingBlock
{
	public function count_data()
	{
		$reporting_data = new ReportingData();
		$reporting_data->set_rows(array(Translation :: get('User'), Translation :: get('LastAccess'), Translation :: get('TotalTime')));
		require_once Path :: get_user_path() . 'trackers/visit_tracker.class.php';

        $course_id = $this->get_course_id();
        $user_id = $this->get_user_id();      
        $tool = $this->get_tool();
        $pid = $this->get_pid();

        $udm = UserDataManager :: get_instance();

        if (isset($user_id))
        {
            $conditions[] = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*' . Tool::PARAM_PUBLICATION_ID . '=' . $pid . '*');
            $conditions[] = new EqualityCondition(VisitTracker :: PROPERTY_USER_ID, $user_id);
            $condition = new AndCondition($conditions);
        }
        else
        {
            $condition = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*&' . Tool::PARAM_PUBLICATION_ID . '=' . $pid . '*');
        }
        $user = $udm->retrieve_user($user_id);

        $arr = self :: visit_tracker_to_array($condition, $user);
		
        $i = 1;
        foreach ($arr[Translation :: get('User')] as $category)
		{
			$reporting_data->add_category($i);
			$i++;
		}
		
		foreach ($arr as $row_name => $row_data)
		{
			foreach ($row_data as $category_id => $category_value)
			{
				$reporting_data->add_data_category_row($category_id + 1, $row_name, $category_value);
			}
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
        $modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        $modes[ReportingFormatter::DISPLAY_TABLE] = Translation :: get('Table');
        return $modes;
	}
}
?>