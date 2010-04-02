<?php
require_once dirname (__FILE__) . '/../weblcms_tool_reporting_block.class.php';
require_once PATH::get_reporting_path() . '/lib/reporting_data.class.php';

class WeblcmsLearningPathAttemptsReportingBlock extends WeblcmsToolReportingBlock
{
	public function count_data()
	{
		$reporting_data = new ReportingData();
		$reporting_data->set_rows(array(Translation :: get('Progress'), Translation :: get('Details')));
		
		$data = array();
		
		$pid = $this->get_pid();
		$course_id = $this->get_course_id();

        $conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_COURSE_ID, $course_id);
        $conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_LP_ID, $pid);
        $condition = new AndCondition($conditions);

        $udm = UserDataManager :: get_instance();

        $dummy = new WeblcmsLpAttemptTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        foreach ($trackers as $tracker)
        {
            $url = $params['url'] . '&attempt_id=' . $tracker->get_id();
            $delete_url = $url . '&stats_action=delete_lp_attempt';

            $user = $udm->retrieve_user($tracker->get_user_id());
            $data[Translation :: get('User')] = $user->get_fullname();
            $data[Translation :: get('Progress')] = $tracker->get_progress() . '%';
            $action = '<a href="' . $url . '">' . Theme :: get_common_image('action_reporting') . '</a>';
            $data[' '][] = Text :: create_link($url, Theme :: get_common_image('action_reporting')) . ' ' . Text :: create_link($delete_url, Theme :: get_common_image('action_delete'));
            
            $reporting_data->add_category($user->get_fullname());
            $reporting_data->add_data_category_row($user->get_fullname(), Translation :: get('Progress'), $tracker->get_progress() . '%');
            $reporting_data->add_data_category_row($user->get_fullname(), Translation :: get('Details'), $action);
        }
		
      	
      	
        return $reporting_data;
	}	
	
	public function retrieve_data()
	{
		return $this->count_data();		
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