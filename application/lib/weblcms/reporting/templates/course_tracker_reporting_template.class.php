<?php
/**
 * $Id: course_tracker_reporting_template.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.reporting.templates
 */
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/../blocks/weblcms_last_access_to_tools_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/weblcms_average_learning_path_score_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/weblcms_average_exercise_score_reporting_block.class.php';
require_once dirname(__FILE__) . '/../../weblcms_manager/weblcms_manager.class.php';

class CourseTrackerReportingTemplate extends ReportingTemplate
{

    function CourseTrackerReportingTemplate($parent)
    {
        parent :: __construct($parent);
        
    	$this->add_reporting_block($this->get_last_access_to_tool());
    	$this->add_reporting_block($this->get_average_learning_path_score());
    	//$this->add_reporting_block($this->get_average_exercise_score());
    }
    
	function display_context()
	{
		
	}
	
	function get_application()
    {
    	return WeblcmsManager::APPLICATION_NAME;
    }
    
    function get_last_access_to_tool()
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
    
    function get_average_learning_path_score()
    {
    	$course_weblcms_block = new WeblcmsAverageLearningPathScoreReportingBlock($this);
    	$course_id = Request :: get(WeblcmsManager::PARAM_COURSE);
    	if ($course_id)
    	{
    		$course_weblcms_block->set_course_id($course_id);
    		$this->add_parameters(WeblcmsManager::PARAM_COURSE, $course_id);
    	}
    	return $course_weblcms_block;
    }
    
    function get_average_exercise_score()
    {
    	$course_weblcms_block = new WeblcmsAverageExerciseScoreReportingBlock($this);
    	$course_id = Request :: get(WeblcmsManager::PARAM_COURSE);
    	if ($course_id)
    	{
    		$course_weblcms_block->set_course_id($course_id);
    		$this->add_parameters(WeblcmsManager::PARAM_COURSE, $course_id);
    	}
    	return $course_weblcms_block;
    }
}
?>