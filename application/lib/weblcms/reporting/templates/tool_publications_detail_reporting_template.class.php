<?php
/**
 * $Id: tool_publications_detail_reporting_template.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.reporting.templates
 */
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/../blocks/weblcms_tool_publications_reporting_block.class.php';
require_once dirname(__FILE__) . '/../../weblcms_manager/weblcms_manager.class.php';

class ToolPublicationsDetailReportingTemplate extends ReportingTemplate
{

    function ToolPublicationsDetailReportingTemplate($parent)
    {
        parent :: __construct($parent);
        $this->add_reporting_block($this->get_tool_publications());
    }
    
	function display_context()
	{
		
	}
	
	function get_application()
    {
    	return WeblcmsManager::APPLICATION_NAME;
    }
    
    function get_tool_publications()
    {
    	$course_weblcms_block = new WeblcmsToolPublicationsReportingBlock($this);

        $course_id = Request :: get(WeblcmsManager::PARAM_COURSE);
    	$user_id = Request :: get(WeblcmsManager::PARAM_USERS);
    	$tool = Request :: get(WeblcmsManager::PARAM_TOOL);
    	
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
    	if ($tool)
    	{
    		$course_weblcms_block->set_tool($tool);
    		$this->add_parameters(WeblcmsManager::PARAM_TOOL, $tool);
    	}
    	return $course_weblcms_block;
    }
}
?>