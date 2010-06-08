<?php
require_once dirname(__FILE__) . '/weblcms_course_reporting_block.class.php';

abstract class WeblcmsToolReportingBlock extends WeblcmsCourseReportingBlock
{
	private $params = array();
	
	function get_user_id()
	{
		return $this->get_parent()->get_parameter(WeblcmsManager::PARAM_USERS);
	}

	function get_tool()
	{
		return $this->get_parent()->get_parameter(WeblcmsManager::PARAM_TOOL);
	}
	
	function get_pid()
	{
		return $this->get_parent()->get_parameter(Tool :: PARAM_PUBLICATION_ID);
	}
	
	function get_params()
	{
		return $this->params;
	}
	
	function set_params($course_id, $user_id, $tool, $pid)
	{
		$this->params['course_id'] = $course_id;
		$this->params['user_id'] = $user_id;
		$this->params['tool'] = $tool;
		$this->params[Tool :: PARAM_PUBLICATION_ID] = $pid;
	}
}
?>