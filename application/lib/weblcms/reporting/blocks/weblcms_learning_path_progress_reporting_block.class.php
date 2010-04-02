<?php
require_once dirname (__FILE__) . '/../weblcms_tool_reporting_block.class.php';
require_once PATH::get_reporting_path() . '/lib/reporting_data.class.php';

class WeblcmsLearningPathProgressReportingBlock extends WeblcmsToolReportingBlock
{
	private $attempt_id;
	
	public function get_attempt_id()
	{
		return $this->attempt_id;
	}
	
	public function set_attempt_id($attempt_id)
	{
		$this->attempt_id = $attempt_id;
	} 
	
	public function count_data()
	{
		$reporting_data = new ReportingData();
     	
		$data = array();
        $objects = $params['objects'];
        $attempt_data = $params['attempt_data'];
        $url = $params['url'];
        $total = 0;
        
        $course_id = $this->get_course_id();
        $tool = $this->get_tool();
        $user_id = $this->get_user_id();
        $attempt_id = $this->get_attempt_id();
        
        if ($course_id)
        {
            $tracker = $this->retrieve_tracker($attempt_id);
            $attempt_data = $this->retrieve_tracker_items($tracker);
        	
        	$object = $objects[$course_id];
            $tracker_datas = $attempt_data[$course_id];

            foreach ($tracker_datas['trackers'] as $tracker)
            {
                /*if (get_class($object) == 'Assessment')
                {
                    $data[' '][] = '<a href="' . $url . '&cid=' . $course_id . '&details=' . $tracker->get_id() . '">' . Theme :: get_common_image('action_view_results') . '</a>';
                }*/

                $data[Translation :: get('LastStartTime')][] = Utilities :: to_db_date($tracker->get_start_time());
                $data[Translation :: get('Status')][] = Translation :: get($tracker->get_status() == 'completed' ? 'Completed' : 'Incomplete');
                $data[Translation :: get('Score')][] = $tracker->get_score() . '%';
                $data[Translation :: get('Time')][] = Utilities :: format_seconds_to_hours($tracker->get_total_time());
                $total += $tracker->get_total_time();

                if ($params['delete'])
                    $data['  '][] = Text :: create_link($params['url'] . '&stats_action=delete_lpi_attempt&delete_id=' . $tracker->get_id(), Theme :: get_common_image('action_delete'));
            }

            $data[Translation :: get('LastStartTime')][] = '';

        }
        else
        {
            foreach ($objects as $wrapper_id => $object)
            {
                $tracker_data = $attempt_data[$wrapper_id];

                $data[' '][] = $object->get_icon();
                $data[Translation :: get('Title')][] = '<a href="' . $url . '&cid=' . $wrapper_id . '">' . $object->get_title() . '</a>';

                if ($tracker_data)
                {
                    $data[Translation :: get('Status')][] = Translation :: get($tracker_data['completed'] ? 'Completed' : 'Incomplete');
                    $data[Translation :: get('Score')][] = round($tracker_data['score'] / $tracker_data['size']) . '%';
                    $data[Translation :: get('Time')][] = Utilities :: format_seconds_to_hours($tracker_data['time']);
                    $total += $tracker_data['time'];
                }
                else
                {
                    $data[Translation :: get('Status')][] = 'incomplete';
                    $data[Translation :: get('Score')][] = '0%';
                    $data[Translation :: get('Time')][] = '0:00:00';
                }

                if ($params['delete'])
                    $data['  '][] = Text :: create_link($params['url'] . '&stats_action=delete_lpi_attempts&item_id=' . $wrapper_id, Theme :: get_common_image('action_delete'));
            }

            $data[Translation :: get('Title')][] = '';
        }

        $data[' '][] = '';
        $data[Translation :: get('Status')][] = '<span style="font-weight: bold;">' . Translation :: get('TotalTime') . '</span>';
        $data[Translation :: get('Score')][] = '';
        $data[Translation :: get('Time')][] = '<span style="font-weight: bold;">' . Utilities :: format_seconds_to_hours($total) . '</span>';
		
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