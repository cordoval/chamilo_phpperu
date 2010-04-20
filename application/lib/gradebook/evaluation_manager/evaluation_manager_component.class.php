<?php
class EvaluationManagerComponent extends SubManagerComponent
{

    /**
     * Constructor
     * @param Evluation $evaluation_manager 
     */
    protected function EvaluationManagerComponent($evaluation_manager)
    {
        parent :: __construct($evaluation_manager);
    }
    
    function get_publisher_id()
    {
    	return $this->get_parent()->get_publisher_id();
    }
    
    function get_publication_id()
    {
    	return $this->get_parent()->get_publication_id();
    }
    
    // database
    function retrieve_evaluation($id)
    {
        return $this->get_parent()->retrieve_evaluation($id);
    }
    
    function retrieve_grade_evaluation($id)
    {
    	return $this->get_parent()->retrieve_grade_evaluation($id);
    }
    
    function retrieve_all_evaluations_on_publication($offset = null, $count = null, $order_property = null)
    {
    	return $this->get_parent()->retrieve_all_evaluations_on_publication($offset, $count, $order_property);
    }
    
    function count_all_evaluations_on_publication()
    {
    	return $this->get_parent()->count_all_evaluations_on_publication();
    }
    
    function get_publication()
    {
    	return $this->get_parent()->get_publication();
    }
    
    // URL's
    function get_evaluation_editing_url($evaluation)
    {
    	return $this->get_parent()->get_evaluation_editing_url($evaluation); 
    }
    
    function get_evaluation_deleting_url($evaluation)
    {
    	
    	return $this->get_parent()->get_evaluation_deleting_url($evaluation);
    }
}