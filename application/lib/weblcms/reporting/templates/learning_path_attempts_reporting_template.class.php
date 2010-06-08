<?php
/**
 * $Id: learning_path_attempts_reporting_template.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.reporting.templates
 */
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/../blocks/weblcms_learning_path_attempts_reporting_block.class.php';
require_once dirname(__FILE__) . '/../../weblcms_manager/weblcms_manager.class.php';

class LearningPathAttemptsReportingTemplate extends ReportingTemplate
{
    private $object;

    function LearningPathAttemptsReportingTemplate($parent)
    {
        parent :: __construct($parent);
    	$this->add_reporting_block($this->get_learning_path_attempts());
    }
    
	function display_context()
	{

	}
	
	function get_application()
    {
    	return WeblcmsManager::APPLICATION_NAME;
    }
   
    function get_learning_path_attempts()
    {
    	$course_weblcms_block = new WeblcmsLearningPathAttemptsReportingBlock($this);
    	$course_id = Request :: get(WeblcmsManager::PARAM_COURSE);
    	$pid = Request :: get(WeblcmsManager::PARAM_PUBLICATION);
    	if ($course_id)
    	{
    		$this->set_parameter(WeblcmsManager::PARAM_COURSE, $course_id);
    	}
    	
    	if ($pid)
    	{
    		$this->set_parameter(WeblcmsManager::PARAM_PUBLICATION, $pid);
    	}
    	
    	$tool = Request :: get(WeblcmsManager::PARAM_TOOL);
    	if ($tool)
    	{
    		$this->set_parameter(WeblcmsManager::PARAM_TOOL, $tool);
    	}
    	return $course_weblcms_block;
    }
}
?>