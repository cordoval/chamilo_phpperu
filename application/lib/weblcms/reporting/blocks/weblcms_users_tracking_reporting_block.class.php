<?php
require_once dirname (__FILE__) . '/../weblcms_course_reporting_block.class.php';
require_once PATH::get_reporting_path() . '/lib/reporting_data.class.php';

class WeblcmsUsersTrackingReportingBlock extends WeblcmsCourseReportingBlock
{
	public function count_data()
	{
		$reporting_data = new ReportingData();
		$reporting_data->set_rows(array(Translation :: get('UserName'), Translation :: get('TimeOnCourse'), Translation :: get('LearningPathProgress'), Translation :: get('ExcerciseProgress'), Translation :: get('TotalPublications'), Translation :: get('UserDetail')));
				
		require_once Path :: get_application_path() . '/lib/weblcms/weblcms_data_manager.class.php';
        require_once PATH::get_user_path() . 'trackers/visit_tracker.class.php';

        $course_id = $this->get_course_id();
        $wdm = WeblcmsDataManager :: get_instance();
        $udm = UserDataManager :: get_instance();
        $rdm = RepositoryDataManager :: get_instance();

        $course = $wdm->retrieve_course($course_id);
        $list = $wdm->retrieve_course_user_relations(new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $course_id));
        $tracker = new VisitTracker();
        while ($user_relation = $list->next_result())
        {
            $user_id = $user_relation->get_user();
            unset($conditions);
            unset($condition);
            $conditions[] = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*&course=' . $course_id . '*');
            $conditions[] = new EqualityCondition(VisitTracker :: PROPERTY_USER_ID, $user_id);
            $condition = new AndCondition($conditions);

            $trackerdata = $tracker->retrieve_tracker_items($condition);
            $user = $udm->retrieve_user($user_id);
            
            $params = $this->get_parent()->get_parameters();
        	$params[ReportingManager::PARAM_TEMPLATE_ID] = Reporting::get_name_registration(Utilities::camelcase_to_underscores('CourseStudentTrackerDetailReportingTemplate'), WeblcmsManager::APPLICATION_NAME)->get_id();
        	$params[WeblcmsManager::PARAM_USERS] = $user->get_id();
        	$url = ReportingManager :: get_reporting_template_registration_url_content($this->get_parent()->get_parent(), $params);
                        
            $reporting_data->add_category($user->get_fullname());
         
	       	$reporting_data->add_data_category_row($user->get_fullname(), Translation :: get('UserName'), $user->get_username());
	        $reporting_data->add_data_category_row($user->get_fullname(), Translation :: get('TimeOnCourse'), self :: get_total_time($trackerdata));
	        $reporting_data->add_data_category_row($user->get_fullname(), Translation :: get('LearningPathProgress'), 0);
	        $reporting_data->add_data_category_row($user->get_fullname(), Translation :: get('ExcerciseProgress'), 0);
	        $reporting_data->add_data_category_row($user->get_fullname(), Translation :: get('TotalPublications'), $rdm->count_content_objects(new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $user_id)));
	        $reporting_data->add_data_category_row($user->get_fullname(), Translation :: get('UserDetail'), '<a href="' . $url . '">' . Translation :: get('Detail') . '</a>');
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