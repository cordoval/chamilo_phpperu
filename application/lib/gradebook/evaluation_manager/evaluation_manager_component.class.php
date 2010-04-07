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
    
    function retrieve_evaluation($id)
    {
        return $this->get_parent()->retrieve_evaluation($id);
    }
    
    function retrieve_all_evaluations_on_publication($offset = null, $count = null, $order_property = null)
    {
    	return $this->get_parent()->retrieve_all_evaluations_on_publication($offset, $count, $order_property);
    }
    
    function count_all_evaluations_on_publication()
    {
    	return $this->get_parent()->count_all_evaluations_on_publication();
    }
}