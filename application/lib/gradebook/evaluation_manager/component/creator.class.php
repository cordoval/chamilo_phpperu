<?php
class EvaluationManagerCreatorComponent extends EvaluationManagerComponent
{
	private $parameters;
	
	function run()
	{
		$this->parameters = $this->get_parameters();
		$type = $this->parameters['type'];
        switch ($type)
        {
            case $type == 'internal_item' :
                $this->create_internal_item();
                break;
            case $type == 'evaluation' : 
            	$this->create_evaluation();
            	break;
//            case self :: ACTION_DELETE :
//                $component = EvaluationManagerComponent :: factory('Deleter', $this);
//                break;
//            case self :: ACTION_UPDATE :
//                $component = EvaluationManagerComponent :: factory('Updater', $this);
//                break; 
//            default :
//                $component = EvaluationManagerComponent :: factory('Browser', $this);
//                break;
        }
	}
	
	function create_evaluation()
	{
       	$evaluation = new Evaluation();
        $evaluation->set_user_id($this->parameters['user_id']);
        $evaluation->set_evaluator_id($this->get_user_id());
        $evaluation->set_format_id($this->parameters['values']['format_list']);
        $evaluation->set_evaluation_date(mktime(date()));
        $evaluation->create();
        echo $evaluation->get_id();
        exit;
	}
}