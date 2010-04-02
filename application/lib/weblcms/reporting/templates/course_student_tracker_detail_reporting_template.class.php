<?php
/**
 * $Id: course_student_tracker_detail_reporting_template.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.reporting.templates
 */
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/../blocks/weblcms_user_information_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/weblcms_user_course_statistics_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/weblcms_course_information_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/weblcms_course_user_learning_path_information_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/weblcms_course_user_exercise_information_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/weblcms_last_access_to_tools_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/weblcms_last_access_reporting_block.class.php';
require_once dirname(__FILE__) . '/../../weblcms_manager/weblcms_manager.class.php';

class CourseStudentTrackerDetailReportingTemplate extends ReportingTemplate
{

    function CourseStudentTrackerDetailReportingTemplate($parent)
    {
        parent :: __construct($parent);
        
        $this->add_reporting_block($this->get_user_information());
        $this->add_reporting_block($this->get_user_course_statistics());
        $this->add_reporting_block($this->get_course_information());
        $this->add_reporting_block($this->get_course_user_learning_path_information());
        $this->add_reporting_block($this->get_course_user_exercise_information());
        $this->add_reporting_block($this->get_last_access_to_tools());
        $this->add_reporting_block($this->get_last_access());

        /*$this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("UserInformation"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("UserCourseStatistics"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("CourseInformation"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("CourseUserLearningpathInformation"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("CourseUserExerciseInformation"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsLastAccessToTools"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsLatestAccess"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));*/
        
        
    }
    
	function display_context()
	{
		//publicatie, content_object, application ... 
	}
	
	function get_application()
    {
    	return WeblcmsManager::APPLICATION_NAME;
    }
    
    function get_last_access_to_tools()
    {
    	$course_weblcms_block = new WeblcmsLastAccessToToolsReportingBlock($this);
    	$course_id = Request :: get(WeblcmsManager::PARAM_COURSE);
    	$user_id = request :: get(WeblcmsManager::PARAM_USERS);
    	if ($course_id)
    	{
    		$course_weblcms_block->set_course_id($course_id);
    		$this->add_parameters(WeblcmsManager::PARAM_COURSE, $course_id);
    	}
    	if ($user_id)
    	{
    		$course_weblcms_block->set_user_id($user_id);
    		$this->add_parameters(WeblcmsManager::PARAM_USERS, $user_id);
    	}
    	return $course_weblcms_block;
    }
    
    function get_last_access()
    {
    	$course_weblcms_block = new WeblcmsLastAccessReportingBlock($this);
    	$course_id = Request :: get(WeblcmsManager::PARAM_COURSE);
    	if ($course_id)
    	{
    		$course_weblcms_block->set_course_id($course_id);
    		$this->add_parameters(WeblcmsManager::PARAM_COURSE, $course_id);
    	}
    	
    	$user_id = request :: get(WeblcmsManager::PARAM_USERS);
    	if ($user_id)
    	{
    		$course_weblcms_block->set_user_id($user_id);
    		$this->add_parameters(WeblcmsManager::PARAM_USERS, $user_id);
    	}
    	return $course_weblcms_block;
    }
    
	function get_course_information()
    {
    	$course_weblcms_block = new WeblcmsCourseInformationReportingBlock($this);
    	$course_id = Request :: get(WeblcmsManager::PARAM_COURSE);
    	if ($course_id)
    	{
    		$course_weblcms_block->set_course_id($course_id);
    		$this->add_parameters(WeblcmsManager::PARAM_COURSE, $course_id);
    	}
    	return $course_weblcms_block;
    }
    
    function get_course_user_learning_path_information()
    {
    	$course_weblcms_block = new WeblcmsCourseUserLearningPathInformationReportingBlock($this);
    	/*$course_user_id = Request :: get(WeblcmsManager::PARAM_USERS);
    	if ($course_user_id)
    	{
    		$course_weblcms_block->set_course_id($course_id);
    		$this->add_parameters(WeblcmsManager::PARAM_USERS, $user_id);
    	}*/
    	return $course_weblcms_block;
    }
    
    function get_course_user_exercise_information()
    {
    	$course_weblcms_block = new WeblcmsCourseUserExerciseInformationReportingBlock($this);
    	/*$course_user_id = Request :: get(WeblcmsManager::PARAM_USERS);
    	if ($course_user_id)
    	{
    		$course_weblcms_block->set_course_id($course_id);
    		$this->add_parameters(WeblcmsManager::PARAM_USERS, $user_id);
    	}*/
    	return $course_weblcms_block;
    }
    
    function get_user_information()
    {
    	$course_weblcms_block = new WeblcmsUserInformationReportingBlock($this);
    	$user_id = Request :: get(WeblcmsManager::PARAM_USERS);
    	if ($user_id)
    	{
    		$course_weblcms_block->set_user_id($user_id);
    		$this->add_parameters(WeblcmsManager::PARAM_USERS, $user_id);
    	}
    	return $course_weblcms_block;
    }
    
    function get_user_course_statistics()
    {
    	$course_weblcms_block = new WeblcmsUserCourseStatisticsReportingBlock($this);
    	$course_id = Request :: get(WeblcmsManager::PARAM_COURSE);
    	if ($course_id)
    	{
    		$course_weblcms_block->set_course_id($course_id);
    		$this->add_parameters(WeblcmsManager::PARAM_COURSE, $course_id);
    	}
    	
    	$user_id = Request :: get(WeblcmsManager::PARAM_USERS);
    	if ($user_id)
    	{
    		$course_weblcms_block->set_user_id($user_id);
    		$this->add_parameters(WeblcmsManager::PARAM_USERS, $user_id);
    	}
    	return $course_weblcms_block;
    }
}
?>