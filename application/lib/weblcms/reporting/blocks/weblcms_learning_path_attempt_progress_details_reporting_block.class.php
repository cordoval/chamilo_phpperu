<?php
require_once dirname(__FILE__) . '/../weblcms_tool_reporting_block.class.php';
require_once PATH :: get_reporting_path() . '/lib/reporting_data.class.php';

class WeblcmsLearningPathAttemptProgressDetailsReportingBlock extends WeblcmsToolReportingBlock
{
    private $attempt_id;
    private $parent_id;

    public function get_attempt_id()
    {
        return $this->get_parent()->get_parameter(LearningPathTool::PARAM_ATTEMPT_ID);
    }
    
    public function get_parent_id()
    {
    	return $this->get_parent()->get_parameter(Tool::PARAM_COMPLEX_ID);
    }

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(array(Translation :: get('LastStartTime'), Translation :: get('Status'), Translation :: get('Score'), Translation :: get('Time')));
    	if ($this->get_parent()->get_parameter(Tool::PARAM_ACTION) == LearningPathTool :: ACTION_VIEW_STATISTICS)
        {
            $reporting_data->add_row(Translation :: get('Action'));
        }
        
        $parent_id = $this->get_parent_id();
        
        $attempt_id = $this->get_attempt_id();
        $tracker = $this->retrieve_tracker($attempt_id);
        $attempt_data = $this->retrieve_tracker_items($tracker);
        
        $tool = $this->get_tool();
        $user_id = $this->get_user_id();
        
        $pid = $this->get_pid();
        $publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($pid);
        
        $data = array();
        $menu = new LearningPathTree($publication->get_content_object_id(), null, null, $attempt_data);
        $objects = $menu->get_objects();
        
        $total = 0;
        
        $object = $objects[$parent_id];
        $tracker_datas = $attempt_data[$parent_id];
        
        $i = 1;
        foreach ($tracker_datas['trackers'] as $tracker)
        {
            /*if (get_class($object) == 'Assessment')
                {
                    $data[' '][] = '<a href="' . $url . '&cid=' . $course_id . '&details=' . $tracker->get_id() . '">' . Theme :: get_common_image('action_view_results') . '</a>';
                }*/
            
            $data[Translation :: get('LastStartTime')] = DatetimeUtilities :: format_locale_date(null, $tracker->get_start_time());
            $data[Translation :: get('Status')] = Translation :: get($tracker->get_status() == 'completed' ? 'Completed' : 'Incomplete');
            $data[Translation :: get('Score')] = $tracker->get_score() . '%';
            $data[Translation :: get('Time')] = Utilities :: format_seconds_to_hours($tracker->get_total_time());
            $total += $tracker->get_total_time();

            $category_name = $i;
            $reporting_data->add_category($category_name);
            $reporting_data->add_data_category_row($category_name, Translation :: get('LastStartTime'), DatetimeUtilities :: format_locale_date(null, $tracker->get_start_time()));
            $reporting_data->add_data_category_row($category_name, Translation :: get('Status'), Translation :: get($tracker->get_status() == 'completed' ? 'Completed' : 'Incomplete'));
            $reporting_data->add_data_category_row($category_name, Translation :: get('Score'), $tracker->get_score() . '%');
            $reporting_data->add_data_category_row($category_name, Translation :: get('Time'), Utilities :: format_seconds_to_hours($tracker->get_total_time()));
            
            if ($this->get_parent()->get_parameter(Tool::PARAM_ACTION) == LearningPathTool :: ACTION_VIEW_STATISTICS)
            {
                $params = $this->get_parent()->get_parameters();
	        	$params[Application::PARAM_ACTION] = WeblcmsManager::ACTION_VIEW_COURSE;
	            $params[Application::PARAM_APPLICATION] = WeblcmsManager::APPLICATION_NAME;
	            $params[Tool::PARAM_ACTION] = LearningPathTool::ACTION_VIEW_STATISTICS;            
	            $params[LearningPathToolStatisticsViewerComponent::PARAM_STAT] = LearningPathToolStatisticsViewerComponent::ACTION_DELETE_LPI_ATTEMPT;
	            $params[LearningPathToolStatisticsViewerComponent::PARAM_DELETE_ID] = $tracker->get_id();
	            $url = Redirect :: get_url($params);
	            
            	$reporting_data->add_data_category_row($category_name, Translation :: get('Action'), Text :: create_link($url, Theme :: get_common_image('action_delete')));
            }
            $i ++;
        }
        $category = '-';
        $reporting_data->add_category($category);
        $reporting_data->add_data_category_row($category, Translation :: get('LastStartTime'), '');
        $reporting_data->add_data_category_row($category, Translation :: get('Status'), '<span style="font-weight: bold;">' . Translation :: get('TotalTime') . '</span>');
        $reporting_data->add_data_category_row($category, Translation :: get('Score'), '');
        $reporting_data->add_data_category_row($category, Translation :: get('Time'), '<span style="font-weight: bold;">' . Utilities :: format_seconds_to_hours($total) . '</span>');
        
        $reporting_data->hide_categories();
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_available_displaymodes()
    {
        $modes = array();
        $modes[ReportingFormatter :: DISPLAY_TABLE] = Translation :: get('Table');
        return $modes;
    }

    private function retrieve_tracker()
    {
        $attempt_id = $this->get_attempt_id();
    	if ($this->get_attempt_id())
        {
	    	$condition = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_ID, $attempt_id);
	        $dummy = new WeblcmsLpAttemptTracker();
	        $trackers = $dummy->retrieve_tracker_items($condition);
	        return $trackers[0];
        }
        else {
        	$pid = $this->get_pid();
       		$publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($pid);
        	
        	$conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_COURSE_ID, $this->get_course_id());
	        $conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_LP_ID, $publication->get_content_object_id());
	        $conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_USER_ID, Session::get_user_id());
	        //$conditions[] = new NotCondition(new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_PROGRESS, 100));
	        $condition = new AndCondition($conditions);
	        
	        $dummy = new WeblcmsLpAttemptTracker();
	        $trackers = $dummy->retrieve_tracker_items($condition);
	        $lp_tracker = $trackers[0];
	        
	        if (! $lp_tracker)
	        {
	            $return = Events :: trigger_event('attempt_learning_path', 'weblcms', array('user_id' => Session::get_user_id(), 'course_id' => $this->get_course_id(), 'lp_id' => $publication->get_content_object_id()));
	            $lp_tracker = $return[0];
	        }
	        
	        return $lp_tracker;
	    }
    }
    

    private function retrieve_tracker_items($lp_tracker)
    {
        $lpi_attempt_data = array();
        
        $condition = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_LP_VIEW_ID, $lp_tracker->get_id());
        
        $dummy = new WeblcmsLpiAttemptTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        
        foreach ($trackers as $tracker)
        {
            $item_id = $tracker->get_lp_item_id();
            if (! $lpi_attempt_data[$item_id])
            {
                $lpi_attempt_data[$item_id]['score'] = 0;
                $lpi_attempt_data[$item_id]['time'] = 0;
            }
            
            $lpi_attempt_data[$item_id]['trackers'][] = $tracker;
            $lpi_attempt_data[$item_id]['size'] ++;
            $lpi_attempt_data[$item_id]['score'] += $tracker->get_score();
            if ($tracker->get_total_time())
                $lpi_attempt_data[$item_id]['time'] += $tracker->get_total_time();
            
            if ($tracker->get_status() == 'completed')
                $lpi_attempt_data[$item_id]['completed'] = 1;
            else
                $lpi_attempt_data[$item_id]['active_tracker'] = $tracker;
        }
        //dump($lpi_attempt_data);
        return $lpi_attempt_data;
    
    }
}
?>