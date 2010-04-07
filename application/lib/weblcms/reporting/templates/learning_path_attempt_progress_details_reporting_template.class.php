<?php
/**
 * $Id: learning_path_attempt_progress_reporting_template.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.reporting.templates
 */
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/../blocks/weblcms_learning_path_attempt_progress_details_reporting_block.class.php';
require_once dirname(__FILE__) . '/../../weblcms_manager/weblcms_manager.class.php';

class LearningPathAttemptProgressDetailsReportingTemplate extends ReportingTemplate
{
    private $object;

    function LearningPathAttemptProgressDetailsReportingTemplate($parent /*$id, $params, $trail, $object*/)
    {
        //$this->object = $object;
        parent :: __construct($parent);
        $this->add_reporting_block($this->get_learning_path_progress_details());
        
        //parent :: __construct($parent, $id, $params, $trail);
    }
    
	function display_context()
	{

	}
	
	function get_application()
    {
    	return WeblcmsManager::APPLICATION_NAME;
    }
    
	function get_learning_path_progress_details()
    {
    	$course_weblcms_block = new WeblcmsLearningPathAttemptProgressDetailsReportingBlock($this);
    	$course_id = Request :: get(WeblcmsManager::PARAM_COURSE);
    	if ($course_id)
    	{
    		$course_weblcms_block->set_course_id($course_id);
    		$this->add_parameters(WeblcmsManager::PARAM_COURSE, $course_id);
    	}
    	
    	$tool = Request :: get(WeblcmsManager::PARAM_TOOL);
    	$course_weblcms_block->set_tool($tool);
    	$this->add_parameters(WeblcmsManager::PARAM_TOOL, $tool);
    	
    	$attempt_id = Request :: get(LearningPathTool::PARAM_ATTEMPT_ID);
    	if ($attempt_id)
        {
	    	$course_weblcms_block->set_attempt_id($attempt_id);
	    	$this->add_parameters(LearningPathTool::PARAM_ATTEMPT_ID, $attempt_id);
        }
        
		if ($this->get_parent()->get_action() == Tool::ACTION_VIEW)
		{
			$this->add_parameters('lp_action', 'view_progress');
		}
    	
    	$pid = Request :: get(Tool::PARAM_PUBLICATION_ID);
    	$course_weblcms_block->set_pid($pid);
    	$this->add_parameters(Tool::PARAM_PUBLICATION_ID, $pid);
    	
    	$parent_id = Request :: get(Tool::PARAM_COMPLEX_ID);
    	$course_weblcms_block->set_parent_id($parent_id);
    	$this->add_parameters(Tool::PARAM_COMPLEX_ID, $parent_id);
    	
    	return $course_weblcms_block;
    }
}
   ?>