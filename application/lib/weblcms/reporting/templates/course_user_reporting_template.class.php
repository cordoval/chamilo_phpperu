<?php
/**
 * $Id: course_user_reporting_template.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.reporting.templates
 */
/**
 * @author Michael Kyndt
 * @todo:
 * Template configuration:
 * 2 listboxes: one with available reporting blocks for the app, one with
 * reporting blocks already in template.
 */
require_once dirname(__FILE__) . '/../blocks/weblcms_user_information_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/weblcms_user_course_statistics_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/weblcms_course_information_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/weblcms_course_user_learning_path_information_reporting_block.class.php';
//require_once dirname(__FILE__) . '/../blocks/weblcms_course_user_exercise_information_reporting_block.class.php';
require_once dirname(__FILE__) . '/../../weblcms_manager/weblcms_manager.class.php';

class CourseUserReportingTemplate extends ReportingTemplate
{

    function CourseUserReportingTemplate($parent)
    {
    	parent :: __construct($parent);
    	
    	//$this->add_reporting_block($this->get_course_information());
    	$this->add_reporting_block($this->get_course_user_learning_path_information());
    	//$this->add_reporting_block($this->get_course_user_exercise_information());
    	//$this->add_reporting_block($this->get_user_information());
    	$this->add_reporting_block($this->get_user_course_statistics());
    }
    
	function display_context()
	{
		
	}
	
	function get_application()
    {
    	return WeblcmsManager::APPLICATION_NAME;
    }
    
    function get_course_information()
    {
    	$course_weblcms_block = new WeblcmsCourseInformationReportingBlock($this);
    	$course_user_id = Request :: get(WeblcmsManager::PARAM_USERS);
    	if ($course_user_id)
    	{
    		$this->set_parameter(WeblcmsManager::PARAM_USERS, $user_id);
    	}
    	return $course_weblcms_block;
    }
    
    function get_course_user_learning_path_information()
    {
    	$course_weblcms_block = new WeblcmsCourseUserLearningPathInformationReportingBlock($this);
    	$course_user_id = Request :: get(WeblcmsManager::PARAM_USERS);
    	if ($course_user_id)
    	{
    		$this->set_parameter(WeblcmsManager::PARAM_USERS, $user_id);
    	}
    	return $course_weblcms_block;
    }
    
    /*function get_course_user_exercise_information()
    {
    	$course_weblcms_block = new WeblcmsCourseUserExerciseInformationReportingBlock($this);
    	$course_user_id = Request :: get(WeblcmsManager::PARAM_USERS);
    	if ($course_user_id)
    	{
    		$this->set_parameter(WeblcmsManager::PARAM_USERS, $user_id);
    	}
    	return $course_weblcms_block;
    }*/
    
    function get_user_information()
    {
    	$course_weblcms_block = new WeblcmsUserInformationReportingBlock($this);
    	$course_user_id = Request :: get(WeblcmsManager::PARAM_USERS);
    	if ($course_user_id)
    	{
    		$this->set_parameter(WeblcmsManager::PARAM_USERS, $user_id);
    	}
    	return $course_weblcms_block;
    }
    
    function get_user_course_statistics()
    {
    	$course_weblcms_block = new WeblcmsUserCourseStatisticsReportingBlock($this);
    	$course_user_id = Request :: get(WeblcmsManager::PARAM_USERS);
    	if ($course_user_id)
    	{
    		$this->set_parameter(WeblcmsManager::PARAM_USERS, $user_id);
    	}
    	return $course_weblcms_block;
    }
}
?>