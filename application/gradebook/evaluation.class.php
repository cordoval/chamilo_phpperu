<?php
/*
 * @author Johan Janssens
 */
class Evaluation extends DataClass
{
	const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'evaluation';
    /**
     * Evaluation properties
     */
     const PROPERTY_USER_ID = 'user_id';
     const PROPERTY_EVALUATOR_ID = 'evaluator_id';
     const PROPERTY_FORMAT_ID = 'format_id';
     const PROPERTY_EVALUATION_DATE = 'evaluation_date';
     
	static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_USER_ID, self :: PROPERTY_EVALUATOR_ID, self :: PROPERTY_FORMAT_ID, self :: PROPERTY_EVALUATION_DATE));
    }

    function get_data_manager()
    {
        return GradebookDataManager :: get_instance();
    }
    // getters and setters
	/**
    * Returns the user id of this Evaluation.
    * @return user_id.
    */	
    function get_user_id()
    {
    	return $this->get_default_property(self :: PROPERTY_USER_ID);
    }
    
     /**
     * Sets the user id of this Evaluation.
     * @param user_id
     */
    function set_user_id($user_id)
    {
    	$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }
	/**
    * Returns the evaluator id of this Evaluation.
    * @return  evaluator_id.
    */	
    function get_evaluator_id()
    {
    	return $this->get_default_property(self :: PROPERTY_EVALUATOR_ID);
    }
    
     /**
     * Sets the evaluator of this Evaluation.
     * @param evaluator_id
     */
    function set_evaluator_id($evaluator_id)
    {
    	$this->set_default_property(self :: PROPERTY_EVALUATOR_ID, $evaluator_id);
    }
	/**
    * Returns the format_id of this Evaluation.
    * @return format_id.
    */	
    function get_format_id()
    {
    	return $this->get_default_property(self :: PROPERTY_FORMAT_ID);
    }
    
     /**
     * Sets the format_id of this Evaluation.
     * @param format_id
     */
    function set_format_id($format_id)
    {
    	$this->set_default_property(self :: PROPERTY_FORMAT_ID, $format_id);
    }
	/**
    * Returns the evaluation date of this Evaluation.
    * @return evaluation_date.
    */	
    function get_evaluation_date()
    {
    	return $this->get_default_property(self :: PROPERTY_EVALUATION_DATE);
    }
    
     /**
     * Sets the evaluation date of this Evaluation.
     * @param evaluation_date
     */
    function set_evaluation_date($evaluation_date)
    {
    	$this->set_default_property(self :: PROPERTY_EVALUATION_DATE, $evaluation_date);
    }
    
    function create()
    {
    	if (!parent :: create())
    		return false;
    	return true;
    }
    
    function delete()
    {	
    	$dm = $this->get_data_manager();
    	
    	// check wether there's an internal or an external instance
    	// First we check if the evaluation has an internal instance. If not it must have an external instance.
    	$condition = new EqualityCondition(InternalItemInstance :: PROPERTY_EVALUATION_ID, $this->get_id());
    	$count = $dm->count_internal_item_instance($condition);
    	if($count > 0)
    	{
    		$internal_item_instance = $dm->retrieve_internal_item_instance($condition);
    
			if(!$internal_item_instance->delete())
			{
				return false;
			}	
    	}
    	else
    	{
    		$external_item_instance = $dm->retrieve_external_item_instance($condition);
			if(!$external_item_instance->delete())
			{
				return false;
			}	
    	}
    	
		$condition = new EqualityCondition(GradeEvaluation :: PROPERTY_ID, $this->get_id());
		$grade_evaluation = $dm->retrieve_grade_evaluation($condition);
		
		if(!$grade_evaluation->delete())
		{
			return false;
		}
		
    	parent :: delete();
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>