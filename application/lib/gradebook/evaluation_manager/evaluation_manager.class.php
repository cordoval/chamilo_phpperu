<?php
require_once dirname(__FILE__) . '/evaluation_manager_component.class.php';

class EvaluationManager extends SubManager
{
	const PARAM_ACTION = 'action';
	
	const ACTION_CREATE = 'creator';
	const ACTION_UPDATE = 'updater';
	const ACTION_DELETE = 'deleter';
	
	const TYPE_INTERNAL_ITEM = 'internal_item';
	
	private $parameters;
	
	function EvaluationManager($parent, $action, $parameters)
	{
        parent :: __construct($parent);
        if ($action)
        {
            $this->set_parameter(self :: PARAM_ACTION, $action);
        }
        
        $this->set_parameters($parameters);
        $this->run();
	}
	
	function run()
	{
        $action = $this->get_parameter(self :: PARAM_ACTION);
        switch ($action)
        {
            case self :: ACTION_CREATE :
                $component = EvaluationManagerComponent :: factory('Creator', $this);
                break;
            case self :: ACTION_DELETE :
                $component = EvaluationManagerComponent :: factory('Deleter', $this);
                break;
            case self :: ACTION_UPDATE :
                $component = EvaluationManagerComponent :: factory('Updater', $this);
                break; 
            default :
                $component = EvaluationManagerComponent :: factory('Browser', $this);
                break;
        }
        $component->run();
	}

    function get_application_component_path()
    {
        return Path :: get_application_path() . 'lib/gradebook/evaluation_manager/component/';
    }
    
    function set_parameters($parameters)
    {
    	$this->parameters = $parameters;
    }
    
    function get_parameters()
    {
    	return $this->parameters;
    }
    
    //url creation
}
?>