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
    		$this->set_parameter(WeblcmsManager::PARAM_COURSE, $course_id);
    	}
    	
    	$tool = Request :: get(WeblcmsManager::PARAM_TOOL);
    	$this->set_parameter(WeblcmsManager::PARAM_TOOL, $tool);
    	
    	$attempt_id = Request :: get(LearningPathTool::PARAM_ATTEMPT_ID);
    	if ($attempt_id)
        {
	    	$this->set_parameter(LearningPathTool::PARAM_ATTEMPT_ID, $attempt_id);
        }
        
		if ($this->get_parent()->get_parameter(Tool::PARAM_ACTION) == Tool::ACTION_VIEW)
		{
			$this->set_parameter('lp_action', 'view_progress');
		}
    	
    	$pid = Request :: get(Tool::PARAM_PUBLICATION_ID);
    	$this->set_parameter(Tool::PARAM_PUBLICATION_ID, $pid);
    	
    	$parent_id = Request :: get(Tool::PARAM_COMPLEX_ID);
    	$this->set_parameter(Tool::PARAM_COMPLEX_ID, $parent_id);
    	
    	return $course_weblcms_block;
    }
}
   ?>