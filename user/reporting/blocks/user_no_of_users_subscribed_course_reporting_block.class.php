<?php
require_once dirname (__FILE__) . '/../user_reporting_block.class.php';

class UserNoOfUsersSubscribedCourseReportingBlock extends UserReportingBlock
{
	public function count_data()
	{
        require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';
        $udm = UserDataManager :: get_instance();
        $users = $udm->count_users();

        $wdm = WeblcmsDataManager :: get_instance();
        $courses = $wdm->count_distinct_course_user_relations();

        $arr[Translation :: get('UsersSubscribedToCourse')][] = $courses;
        $arr[Translation :: get('UsersNotSubscribedToCourse')][] = $users - $courses;

        return Reporting :: getSerieArray($arr);
        
        require_once dirname (__FILE__) . '/../user_reporting_block.class.php';
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