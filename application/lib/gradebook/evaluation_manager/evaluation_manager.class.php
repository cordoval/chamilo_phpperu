<?php
require_once dirname(__FILE__) . '/evaluation_manager_component.class.php';
require_once dirname(__FILE__) . '/../gradebook_data_manager.class.php';

class EvaluationManager extends SubManager
{
	const APPLICATION_NAME = 'evaluation';
	
	const PARAM_EVALUATION_ACTION = 'evaluation_action';
	const PARAM_EVALUATION_ID = 'evaluation_id';
	
	const PARAM_PUBLICATION_ID = 'publication_id';
	const PARAM_PUBLISHER_ID = 'publisher_id';
	const PARAM_CONTENT_OBJECT_ID = 'content_object_id';
	
	const ACTION_BROWSE = 'browser';
	const ACTION_CREATE = 'creator';
	const ACTION_UPDATE = 'updater';
	const ACTION_DELETE = 'deleter';
	
	const TYPE_INTERNAL_ITEM = 'internal_item';
	
	private $publisher_id;
	private $publication_id;
	private $trail;
	
	function EvaluationManager($parent, $publication_id, $publisher_id, $action, $trail)
	{
        parent :: __construct($parent);
        if ($action)
        {
            $this->set_parameter(self :: PARAM_EVALUATION_ACTION, $action);
        }
        $this->set_publication_id($publication_id);
        $this->set_publisher_id($publisher_id);
        $this->set_trail($trail);
	}
	
	function run()
	{	
        $action = $this->get_parameter(self :: PARAM_EVALUATION_ACTION);
        switch ($action)
        {
            case self :: ACTION_CREATE :
                $component = $this->create_component_test('Creator', $this->get_publication_id(), $this->get_publisher_id(), $this->get_trail());
                break;
            case self :: ACTION_DELETE :
                $component = $this->create_component_test('Deleter', $this->get_publication_id(), $this->get_publisher_id(), $this->get_trail());
                break;
            case self :: ACTION_UPDATE :
                $component = $this->create_component_test('Updater', $this->get_publication_id(), $this->get_publisher_id(), $this->get_trail());
                break; 
            case self :: ACTION_BROWSE :
            	$component = $this->create_component_test('Browser', $this->get_publication_id(), $this->get_publisher_id(), $this->get_trail());
                break;
            default :
                $component = $this->create_component_test('Browser', $this->get_publication_id(), $this->get_publisher_id(), $this->get_trail());
                break;
        }
        $component->run();
	}

    function get_application_component_path()
    {
        return Path :: get_application_path() . 'lib/gradebook/evaluation_manager/component/';
    }
    
    function set_publisher_id($publisher_id)
    {
    	$this->publisher_id = $publisher_id;
    } 
    
    function get_publisher_id()
    {
    	return $this->publisher_id;	
    }
    
    function set_publication_id($publication_id)
    {
    	$this->publication_id = $publication_id;
    } 
    
    function get_publication_id()
    {
    	return $this->publication_id;	
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
        return GradebookDataManager :: get_instance()->retrieve_all_evaluations_on_publication(Request :: get('application'), $this->get_publication_id(), $offset, $count, $order_property);
    }

    function count_all_evaluations_on_publication()
    {
        return GradebookDataManager :: get_instance()->count_all_evaluations_on_publication($this->get_publication_id());
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
    
    function retrieve_all_active_evaluation_formats()
    {
    	return GradebookDataManager :: get_instance()->retrieve_all_active_evaluation_formats();
    }
    
    function retrieve_evaluation_format($id)
    {
    	return GradebookDataManager :: get_instance()->retrieve_evaluation_format($id);
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
		return $this->get_url(array(self :: PARAM_EVALUATION_ACTION => self :: ACTION_UPDATE, self :: PARAM_EVALUATION_ID => $evaluation->get_id()));
    }
    
    function get_evaluation_deleting_url($evaluation)
    {
		return $this->get_url(array(self :: PARAM_EVALUATION_ACTION => self :: ACTION_DELETE, self :: PARAM_EVALUATION_ID => $evaluation->get_id()));
    }
    
    function create_component_test($type, $publication_id, $publisher_id, $trail)
    {
    	$component = $this->create_component($type);
    	$component->set_publication_id($publication_id);
    	$component->set_publisher_id($publisher_id);
    	$component->set_trail($trail);
    	return $component;
    }
}
?>