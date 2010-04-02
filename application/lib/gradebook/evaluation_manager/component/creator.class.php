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
	
	function create_internal_item()
	{
		$internal_item = new InternalItem();
		$internal_item->set_application($this->parameters['application']);
		$internal_item->set_publication_id($this->parameters['publication_id']);
		$internal_item->set_calculated($this->parameters['calculated']);
		$internal_item->create();
	}
}