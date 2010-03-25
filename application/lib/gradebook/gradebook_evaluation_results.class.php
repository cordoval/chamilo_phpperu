<?php
/*
 * @author Johan Janssens
 */
class GradebookEvaluationResults extends DataClass
{
	const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'evaluation_results';
    
    /**
     * GradebookEvaluationResults properties
     */
    
     const PROPERTY_GRADEBOOK_EVALUATION_ID = 'gradebook_evaluation_id';
     const PROPERTY_USER_ID = 'user_id';
     const PROPERTY_EVALUATOR = 'evaluator_id';
     const PROPERTY_RESULT = 'result';
     const PROPERTY_EVALUATION_DATE = 'evaluation_date';
     
	static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_FORMAT, self :: PROPERTY_RESULT, self :: PROPERTY_EVALUATOR));
    }

    function get_data_manager()
    {
        return GradebookDataManager :: get_instance();
    }
    // getters and setters
	/**
    * Returns the gradebook evaluation id of this GradebookEvaluationResults.
    * @return the gradebook evaluation id.
    */	
    function get_gradebook_evaluation_id()
    {
    	return $this->get_default_property(self :: PROPERTY_GRADEBOOK_EVALUATION_ID);
    }
    
     /**
     * Sets the gradebook evaluation id of this GradebookEvaluationResults.
     * @param gradebook evaluation id
     */
    function set_gradebook_evaluation_id($gradebook_evaluation_id)
    {
    	$this->set_default_property(self :: PROPERTY_GRADEBOOK_EVALUATION_ID, $gradebook_evaluation_id);
    }
/**
    * Returns the user id of this GradebookEvaluationResults.
    * @return the user id.
    */	
    function get_user_id()
    {
    	return $this->get_default_property(self :: PROPERTY_USER_ID);
    }
    
     /**
     * Sets the user id of this GradebookEvaluationResults.
     * @param user id
     */
    function set_user_id($user_id)
    {
    	$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }
	/**
    * Returns the evaluator id of this GradebookEvaluationResults.
    * @return the evaluator id.
    */	
    function get_evaluator_id()
    {
    	return $this->get_default_property(self :: PROPERTY_EVALUATOR_ID);
    }
    
     /**
     * Sets the evaluator of this GradebookEvaluationResults.
     * @param evaluator
     */
    function set_evaluator_id($evaluator_id)
    {
    	$this->set_default_property(self :: PROPERTY_EVALUATOR, $evaluator_id);
    }
	/**
    * Returns the result of this GradebookEvaluationResults.
    * @return the result.
    */	
    function get_result()
    {
    	return $this->get_default_property(self :: PROPERTY_RESULT);
    }
    
     /**
     * Sets the result of this GradebookEvaluationResults.
     * @param result
     */
    function set_result($result)
    {
    	$this->set_default_property(self :: PROPERTY_RESULT, $result);
    }
	/**
    * Returns the evaluation date of this GradebookEvaluationResults.
    * @return the evaluation date.
    */	
    function get_evaluation_date()
    {
    	return $this->get_default_property(self :: PROPERTY_EVALUATION_DATE);
    }
    
     /**
     * Sets the evaluation date of this GradebookEvaluationResults.
     * @param evaluation date
     */
    function set_evaluation_date($evaluation_date)
    {
    	$this->set_default_property(self :: PROPERTY_EVALUATION_DATE, $evaluation_date);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>