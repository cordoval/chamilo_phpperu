<?php
require_once dirname(__FILE__) . '/weblcms_course_reporting_block.class.php';
abstract class WeblcmsToolReportingBlock extends WeblcmsCourseReportingBlock
{
	private $user_id;
	private $tool;
	private $pid;
	private $params = array();
	
	function get_user_id()
	{
		return $this->user_id;
	}
	
	function set_user_id($user_id)
	{
		$this->user_id = $user_id;
	}
	
	function get_tool()
	{
		return $this->tool;
	}
	
	function set_tool($tool)
	{
		$this->tool = $tool;
	}
	
	function get_pid()
	{
		return $this->pid;
	}
	
	function set_pid($pid)
	{
		$this->pid = $pid;
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
		$this->params['pid'] = $pid;
	}
}
?>