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
}