<?php
require_once dirname (__FILE__) . '/../user_reporting_block.class.php';

class UsersTrackingReportingBlock extends UserReportingBlock
{
	public function count_data()
	{
		require_once Path :: get_application_path() . '/lib/weblcms/weblcms_data_manager.class.php';
        require_once (dirname(__FILE__) . '/../trackers/visit_tracker.class.php');

        $course_id = $params[ReportingManager :: PARAM_COURSE_ID];
        $wdm = WeblcmsDataManager :: get_instance();
        $udm = UserDataManager :: get_instance();
        $rdm = RepositoryDataManager :: get_instance();

        $course = $wdm->retrieve_course($course_id);
        $list = $wdm->retrieve_course_user_relations(new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $course_id));
        //$list = $wdm->retrieve_course_users($course);
        $tracker = new VisitTracker();
        while ($user_relation = $list->next_result())
        {
            $user_id = $user_relation->get_user();
            unset($conditions);
            unset($condition);
            $conditions[] = new LikeCondition(VisitTracker :: PROPERTY_LOCATION, '&course=' . $course_id);
            $conditions[] = new EqualityCondition(VisitTracker :: PROPERTY_USER_ID, $user_id);
            $condition = new AndCondition($conditions);

            $trackerdata = $tracker->retrieve_tracker_items($condition);
            $params[ReportingManager :: PARAM_USER_ID] = $user_id;
            $user = $udm->retrieve_user($user_id);
            $arr[Translation :: get('LastName')][] = $user->get_lastname();
            $arr[Translation :: get('FirstName')][] = $user->get_firstname();
            $arr[Translation :: get('TimeOnCourse')][] = self :: get_total_time($trackerdata);
            $arr[Translation :: get('LearningPathProgress')][] = 0;
            $arr[Translation :: get('ExcerciseProgress')][] = 0;
            $arr[Translation :: get('TotalPublications')][] = $rdm->count_content_objects(new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $user_id));
            $url = Reporting :: get_weblcms_reporting_url('CourseStudentTrackerDetailReportingTemplate', $params);
            $arr[Translation :: get('UserDetail')][] = '<a href="' . $url . '">' . Translation :: get('Detail') . '</a>';
        }

        $description[Reporting :: PARAM_ORIENTATION] = Reporting :: ORIENTATION_HORIZONTAL;
        return Reporting :: getSerieArray($arr, $description);
    }	
	
	public function retrieve_data()
	{
		return count_data();		
	}
	
	function get_application()
	{
		return UserManager::APPLICATION_NAME;
	}
}
?>