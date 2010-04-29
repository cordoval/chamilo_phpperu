<?php
require_once dirname(__FILE__) . '/assessment_tool_gradebook_connector.class.php';
require_once dirname(__FILE__) . '/learning_path_tool_gradebook_connector.class.php';

class WeblcmsGradebookConnector extends GradeBookConnector
{	
	function get_tracker_score($publication_id, $tool = null)
	{
		if(!$tool)
		{
			$tool = Request :: get('tool');
		}
		$toolconnector = Utilities :: underscores_to_camelcase($tool) . 'ToolGradebookConnector';
		$toolconnectorclass = new $toolconnector();
		return $toolconnectorclass->get_tracker_score($application, $publication_id);
	}
	
	function get_tracker_user($publication_id, $tool = null)
	{
		if(!$tool)
		{
			$tool = Request :: get('tool');
		}
		$toolconnector = Utilities :: underscores_to_camelcase($tool) . 'ToolGradebookConnector';
		$toolconnectorclass = new $toolconnector();
		return($toolconnectorclass->get_tracker_user($publication_id));
	}
	
	function get_tracker_date($publication_id, $tool = null)
	{
		if(!$tool)
		{
			$tool = Request :: get('tool');
		}
		$toolconnector = Utilities :: underscores_to_camelcase($tool) . 'ToolGradebookConnector';
		$toolconnectorclass = new $toolconnector();
		return($toolconnectorclass->get_tracker_date($publication_id));
	}
//	
//	function get_tracker_id($publication_id, $tool = null)
//	{
//		if(!$tool)
//		{
//			$tool = Request :: get('tool');
//		}
//		$toolconnector = Utilities :: underscores_to_camelcase($tool) . 'ToolGradebookConnector';
//		$toolconnectorclass = new $toolconnector();
//		return($toolconnectorclass->get_tracker_id($publication_id));
//	}
//	
}
?>