<?php
require_once dirname (__FILE__) . '/../weblcms_tool_reporting_block.class.php';
require_once PATH::get_reporting_path() . '/lib/reporting_data.class.php';

class WeblcmsLastAccessToToolsReportingBlock extends WeblcmsToolReportingBlock
{
	public function count_data()
	{
		$reporting_data = new ReportingData();
        $reporting_data->set_rows(array(Translation :: get('Tool'), Translation :: get('LastAccess'), Translation :: get('Clicks'), Translation :: get('Publications')));
		
		require_once Path :: get_user_path() . 'trackers/visit_tracker.class.php';

        $wdm = WeblcmsDataManager :: get_instance();
        $tracker = new VisitTracker();
        $course_id = $this->get_course_id();
        $user_id = $this->get_user_id();
        $tools = $wdm->get_course_modules($course_id);

        foreach ($tools as $key => $value)
        {
            $name = $value->name;
            $tool = Translation :: get(Utilities :: underscores_to_camelcase($name));
            //$link = '<img src="'.Theme :: get_image_path('weblcms').'tool_'.$name.'.png" style="vertical-align: middle;" />';// <a href="run.php?go=courseviewer&course='.$course_id.'&tool='.$name.'&application=weblcms">'.Translation :: get(Utilities::underscores_to_camelcase($name)).'</a>';
            $params = array();
            $params[Application::PARAM_ACTION] = WeblcmsManager::ACTION_VIEW_COURSE;
            $params[Application::PARAM_APPLICATION] = WeblcmsManager::APPLICATION_NAME;
            $params[WeblcmsManager::PARAM_COURSE] = $this->get_course_id();
            $params[WeblcmsManager::PARAM_TOOL] = $name;
            
            $link = ' <a href="' . Redirect::get_url($params) . '">' . Translation :: get('access') . '</a>';
            $date = $wdm->get_last_visit_date_per_course($course_id, $name);
            if ($date)
            {
                $date = date('d F Y (G:i:s)', $date);
            }
            else
            {
                $date = Translation :: get('NeverAccessed');
            }
            $conditions = array();
            $conditions2 = array();
            $conditions3 = array();
            $conditions[] = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*course=' . $course_id . '*tool=' . $name . '*');
            if (isset($user_id))
                $conditions[] = new EqualityCondition(VisitTracker :: PROPERTY_USER_ID, $user_id);
            $conditions2[] = new AndCondition($conditions);

            if ($name == 'reporting')
            {
                $conditions3[] = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*course_id???=' . $course_id . '*');
                if (isset($user_id))
                    $conditions3[] = new EqualityCondition(VisitTracker :: PROPERTY_USER_ID, $user_id);
                $conditions2[] = new AndCondition($conditions3);
            }
            $condition = new OrCondition($conditions2);

            $trackerdata = $tracker->retrieve_tracker_items($condition);

            $params = $this->get_parent()->get_parameters();
        	$params[ReportingManager::PARAM_TEMPLATE_ID] = Reporting::get_name_registration(Utilities::camelcase_to_underscores('ToolPublicationsDetailReportingTemplate'), WeblcmsManager::APPLICATION_NAME)->get_id();
        	$params[WeblcmsManager::PARAM_USERS] = $user_id;
        	$params[WeblcmsManager::PARAM_TOOL] = $name;
            //$url = ReportingManager :: get_reporting_template_registration_url_content($this->get_parent()->get_parent(), $params);
            $url = $this->get_parent()->get_url($params);
            
            $link_pub = '<a href="' . $url . '">' . Translation :: get('ViewPublications') . '</a>';
            $reporting_data->add_category($tool);
            $reporting_data->add_data_category_row($tool, Translation :: get('Tool'), $link);
        	$reporting_data->add_data_category_row($tool, Translation :: get('LastAccess'), $date);
        	$reporting_data->add_data_category_row($tool, Translation :: get('Clicks'), count($trackerdata));
        	$reporting_data->add_data_category_row($tool, Translation :: get('Publications'), $link_pub);
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
        //$modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        $modes[ReportingFormatter::DISPLAY_TABLE] = Translation :: get('Table');
        return $modes;
	}
}
?>