<?php
require_once dirname (__FILE__) . '/../weblcms_tool_reporting_block.class.php';
require_once PATH::get_reporting_path() . '/lib/reporting_data.class.php';

class WeblcmsToolPublicationsReportingBlock extends WeblcmsToolReportingBlock
{
	public function count_data()
	{
		$reporting_data = new ReportingData();
        $reporting_data->set_rows(array(Translation :: get('Title'), Translation :: get('Description'), Translation :: get('LastAccess'), Translation :: get('TotalTimesAccessed'), Translation :: get('PublicationDetails')));
		
		require_once Path :: get_user_path() . 'trackers/visit_tracker.class.php';

		$course_id = $this->get_course_id();
		$user_id = $this->get_user_id();
		$tool = $this->get_tool();

        $tracker = new VisitTracker();
        $wdm = WeblcmsDataManager :: get_instance();

        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course_id);
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, $tool);

        $access = array();
        $access[] = new InCondition('user_id', $user_id, $wdm->get_database()->get_alias('content_object_publication_user'));
        if (! empty($user_id))
        {
            $access[] = new EqualityCondition('user_id', null, $wdm->get_database()->get_alias('content_object_publication_user'));
        }
        $conditions[] = new OrCondition($access);
        $condition = new AndCondition($conditions);
        $lops = $wdm->retrieve_content_object_publications_new($condition, $params['order_by']);
		$i = 1;
        
        while ($lop = $lops->next_result())
        {
            $condition = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*course=' . $course_id . '*' . Tool::PARAM_PUBLICATION_ID . '=' . $lop->get_id() . '*');
            $trackerdata = $tracker->retrieve_tracker_items($condition);

            foreach ($trackerdata as $key => $value)
            {
                if ($value->get_leave_date() > $lastaccess)
                    $lastaccess = $value->get_leave_date();
            }
            $params = array();
            $params[Application::PARAM_ACTION] = WeblcmsManager::ACTION_VIEW_COURSE;
            $params[Application::PARAM_APPLICATION] = WeblcmsManager::APPLICATION_NAME;
            $params[WeblcmsManager::PARAM_COURSE] = $course_id;
            $params[WeblcmsManager::PARAM_TOOL] = $tool;
            $params[WeblcmsManager::PARAM_PUBLICATION] = $lop->get_id();           
            $url = Redirect::get_url($params);
            
            $des = $lop->get_content_object()->get_description();
            $this->set_pid($lop->get_id());
            $this->set_params($course_id, $user_id, $tool, $this->get_pid());

			$params = $this->get_parent()->get_parameters();	
            $params[ReportingManager::PARAM_TEMPLATE_ID] = Reporting::get_name_registration(Utilities::camelcase_to_underscores('PublicationDetailReportingTemplate'), WeblcmsManager::APPLICATION_NAME)->get_id();
            $params[WeblcmsManager::PARAM_COURSE] = $course_id;
            $params[WeblcmsManager::PARAM_USERS] = $user_id;
            $params[WeblcmsManager::PARAM_TOOL] = $tool;
            $params[WeblcmsManager::PARAM_PUBLICATION] = $lop->get_id();   
            $url_detail = ReportingManager :: get_reporting_template_registration_url_content($this->get_parent()->get_parent(), $params);
                        
            $reporting_data->add_category($i);
            $reporting_data->add_data_category_row($i, Translation :: get('Title'), '<a href="' . $url . '">' . $lop->get_content_object()->get_title() . '</a>');
		    $reporting_data->add_data_category_row($i, Translation :: get('Description'), Utilities :: truncate_string($des, 50));
		    $reporting_data->add_data_category_row($i, Translation :: get('LastAccess'), $lastaccess);
		    $reporting_data->add_data_category_row($i, Translation :: get('TotalTimesAccessed'), count($trackerdata));
		    $reporting_data->add_data_category_row($i, Translation :: get('PublicationDetails'), '<a href="' . $url_detail . '">' . Translation :: get('AccessDetails') . '</a>');	     
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
        $modes[ReportingChartFormatter::DISPLAY_PIE] = Translation :: get('Chart:Pie');
        return $modes;
	}
}
?>