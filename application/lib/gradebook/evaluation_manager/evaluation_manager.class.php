<?php
require_once dirname(__FILE__) . '/evaluation_manager_component.class.php';
require_once dirname(__FILE__) . '/../gradebook_data_manager.class.php';

class EvaluationManager extends SubManager
{
	const APPLICATION_NAME = 'evaluation';
	
	const PARAM_ACTION = 'action';
	const PARAM_EVALUATION_ID = 'evaluation_id';
	const PARAM_PUBLICATION_ID = 'publication_id';
	
	const PARAM_PARAMETERS = 'parameters';
	
	const ACTION_BROWSE = 'browser';
	const ACTION_CREATE = 'creator';
	const ACTION_UPDATE = 'updater';
	const ACTION_DELETE = 'deleter';
	
	const TYPE_INTERNAL_ITEM = 'internal_item';
	
	private $parameters;
	private $publication;
	private $trail;
	
	function EvaluationManager($parent, $publication, $action, $trail)
	{
        parent :: __construct($parent);
        if ($action)
        {
            $this->set_parameter(self :: PARAM_ACTION, $action);
        }
        $this->set_publication($publication);
        $this->set_trail($trail);
//        $this->set_parameters($parameters);
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
            case self :: ACTION_BROWSE :
            	$component = EvaluationManagerComponent :: factory('Browser', $this);
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
    
    function set_publication($publication)
    {
    	$this->publication = $publication;
    }
    
    function get_publication()
    {
    	return $this->publication;
    }
    
    function set_trail($trail)
    {
    	$this->trail = $trail;
    }
    
    function get_trail()
    {
    	return $this->trail;
    }
    
    // database
    function retrieve_all_evaluations_on_publication($offset = null, $count = null, $order_property = null)
    {
    	return GradebookDataManager :: get_instance()->retrieve_all_evaluations_on_publication($this->get_publication()->get_id(), $offset, $count, $order_property);
    }
    
    function count_all_evaluations_on_publication()
    {
    	return GradebookDataManager :: get_instance()->count_all_evaluations_on_publication($this->get_publication()->get_id());
    }
    
    function retrieve_evaluations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return GradebookDataManager :: get_instance()->retrieve_evaluation($condition, $offset, $count, $order_property);
    }

    function retrieve_evaluation($id)
    {
        return GradebookDataManager :: get_instance()->retrieve_evaluation($id);
    }
    
    function retrieve_grade_evaluation($id)
    {
    	return GradebookDataManager :: get_instance()->retrieve_grade_evaluation($id);
    }
    
    function retrieve_internal_item_by_publication($application, $publication_id)
    {
    	return GradebookDataManager :: get_instance()->retrieve_internal_item_by_publication($application, $publication_id);
    }
    
 	function retrieve_evaluation_ids_by_publication($application, $publication_id)
 	{
 		return GradebookDataManager :: get_instance()->retrieve_evaluation_ids_by_publication($application, $publication_id);
 	}
 	
 	function move_internal_to_external($application, $publication)
 	{
 		return GradebookDataManager :: get_instance()->move_internal_to_external($application, $publication);
 	}
    
    //url creation
    function get_evaluation_editing_url($evaluation)
    {
    	$parameters[self :: PARAM_EVALUATION_ID] = $evaluation->get_id();
    	$parameters[self :: PARAM_PUBLICATION_ID] = $this->get_publication()->get_id();
    	$code = base64_encode(serialize($parameters));
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE, self :: PARAM_PARAMETERS => $code));
    }
    
    function get_evaluation_deleting_url($evaluation)
    {
    	$parameters[self :: PARAM_EVALUATION_ID] = $evaluation->get_id();
    	$parameters[self :: PARAM_PUBLICATION_ID] = $this->get_publication()->get_id();
    	$code = base64_encode(serialize($parameters));
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE, self :: PARAM_PARAMETERS => $code));
    }
}
?>