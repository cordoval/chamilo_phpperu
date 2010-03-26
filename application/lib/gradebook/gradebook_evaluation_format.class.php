<?php
/*
 * @author Johan Janssens
 */
class GradebookEvaluationFormat extends DataClass
{
	const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'evaluation_format';
    
    /**
     * GradebookEvaluationFormat properties
     */
    const PROPERTY_EVALUATION_FORMAT = 'evaluation_format';
    const PROPERTY_ACTIVE = 'active';
    /*
     * Possible active values
     */
    const EVALUATION_FORMAT_NON_ACTIVE = 0;
    const EVALUATION_FORMAT_ACTIVE = 1;
    
	static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_EVALUATION_FORMAT, self :: PROPERTY_ACTIVE));
    }

    function get_data_manager()
    {
        return GradebookDataManager :: get_instance();
    }
    // getters and setters
	/**
    * Sets the evaluation format of this GradebookEvaluationFormat.
    */	
    function set_evaluation_format($evaluation_format)
    {
    	$this->set_default_property(self :: PROPERTY_EVALUATION_FORMAT, $evaluation_format);
    }  
	/**
    * Returns the evaluation format of this GradebookEvaluationFormat.
    * @return the format.
    */	
    function get_evaluation_form()
    {
    	return $this->get_default_property(self :: PROPERTY_EVALUATION_FORMAT);
    }

	/**
    * Sets the active property of this GradebookEvaluationFormat.
    */	
    function set_active($active)
    {
    	$this->set_default_property(self :: PROPERTY_ACTIVE, $active);
    }  
	/**
    * Returns the active property of this GradebookEvaluationFormat.
    * @return the format.
    */	
    function get_active()
    {
    	return $this->get_default_property(self :: PROPERTY_ACTIVE);
    }
    
    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
    
	function create()
    {
    	if(!parent :: create())
    	{
    		return false;
    	}
    	
    	return true;
    }
}
?>