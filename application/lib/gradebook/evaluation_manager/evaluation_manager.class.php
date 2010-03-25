<?php
class EvaluationManager extends SubManager
{
	const PARAM_ACTION = 'action';
	
	const ACTION_BROWSE_EVALUATIONS = 'browse_evaluations';
	const ACTION_CREATE_EVALUATIONS = 'create_evaluation';
	const ACTION_DELETE_EVALUATIONS = 'delete_evaluation';
	const ACTION_UPDATE_EVALUATIONS = 'update_evaluation';
	
	const EVALUATION_PARAMETERS = 'evaluation_parameters';
	
	function EvaluationManager($gradebook_manager)
	{
		$action = Request :: get(self :: PARAM_ACTION);
        if ($action)
        {
            $this->set_parameter(self :: PARAM_ACTION, $action);
        }
        $this->parse_input_from_table();
	}
	
	function run()
	{
		$action = $this->get_parameter( self::PARAM_ACTION );
		
		switch ($action)
		{
			case self :: ACTION_BROWSE_EVALUATIONS:
				$component = EvaluationManagerComponent :: factory('Browser', $this);
				break;
			case self :: ACTION_CREATE_EVALUATIONS:
				$component = EvaluationManagerComponent :: factory('Creator', $this);
				break;
			case self :: ACTION_DELETE_EVALUATIONS:
				$component = EvaluationManagerComponent :: factory('Deleter', $this);
				break;
			case self :: ACTION_UPDATE_EVALUATIONS:
				$component = EvaluationManagerComponent :: factory('Updater', $this);
				break;
		}
		$component->run();
	}
	
	function get_application_component_path()
	{
		return Path :: get_application_path . 'lib/gradebook/evaluation_manager/component/';
	}
	
	private function parse_input_from_table()
	{
		
	}
}
?>