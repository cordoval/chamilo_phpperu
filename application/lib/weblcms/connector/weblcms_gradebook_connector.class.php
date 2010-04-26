<?php
require_once dirname(__FILE__) . '/assessment_tool_gradebook_connector.class.php';
require_once dirname(__FILE__) . '/learning_path_tool_gradebook_connector.class.php';

class WeblcmsGradebookConnector extends GradeBookConnector
{	
	function get_tracker_score($application, $publication_id)
	{
		$tool = Request :: get('tool');
		$toolconnector = Utilities :: underscores_to_camelcase($tool) . 'ToolGradebookConnector';
		$toolconnectorclass = new $toolconnector();
		return $toolconnectorclass->get_tracker_score($application, $publication_id);
	}
	
	function get_tracker_user($application, $publication_id)
	{
		$tool = Request :: get('tool');
		$toolconnector = Utilities :: underscores_to_camelcase($tool) . 'ToolGradebookConnector';
		$toolconnectorclass = new $toolconnector();
		return($toolconnectorclass->get_tracker_user($application, $publication_id));
	}
}
?>