<?php
/**
 * $Id: learning_path_progress_reporting_template.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.reporting.templates
 */
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/../blocks/weblcms_learning_path_progress_reporting_block.class.php';
require_once dirname(__FILE__) . '/../../weblcms_manager/weblcms_manager.class.php';

class LearningPathProgressReportingTemplate extends ReportingTemplate
{
    private $object;

    function LearningPathProgressReportingTemplate($parent /*$id, $params, $trail, $object*/)
    {
        //$this->object = $object;
        parent :: __construct($parent);
        $this->add_reporting_block($this->get_learning_path_progress());
        
        //parent :: __construct($parent, $id, $params, $trail);
    }
    
	function display_context()
	{
		//publicatie, content_object, application ... 
	}
	
	function get_application()
    {
    	return WeblcmsManager::APPLICATION_NAME;
    }
    
    function get_learning_path_progress()
    {
    	$course_weblcms_block = new WeblcmsLearningPathProgressReportingBlock($this);
    	$course_id = Request :: get(WeblcmsManager::PARAM_COURSE);
    	if ($course_id)
    	{
    		$course_weblcms_block->set_course_id($course_id);
    		$this->add_parameters(WeblcmsManager::PARAM_COURSE, $course_id);
    	}
    	
    	$tool = Request :: get(WeblcmsManager::PARAM_TOOL);
    	$course_weblcms_block->set_tool($tool);
    	
    	$user_id = Request :: get(WeblcmsManager::PARAM_USERS);
    	$course_weblcms_block->set_course_id($user_id);
    	
    	$attempt_id = Request :: get(LearningPathTool::PARAM_ATTEMPT_ID);
    	$course_weblcms_block->set_attempt_id($attempt_id);
    	
    	return $course_weblcms_block;
    }
}
?>