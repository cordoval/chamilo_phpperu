<?php
/*
 * @author Ben Vanmassenhove
 */
class GradebookExternalEvaluation extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'external_evaluation';
    /*
     * GradebookEvaluationGrades properties
     */
    const PROPERTY_EXTERNAL_GRADE_ID = 'external_grade_id';
    const PROPERTY_TITEL = 'titel';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_FORMAT_ID = 'format_id';
	/**
     * Get the default properties
     * @return array The property names.
     */
    static function get_defualt_property_names()
    {
    	return parent :: get_defualt_property_names(array(self :: PROPERTY_EXTERNAL_GRADE, self :: PROPERTY_TITEL, self :: PROPERTY_DESCRIPTION, self :: PROPERY_FORMAT));
    }
    
    function get_data_manager()
    {
    	return GradebookDataManager :: get_instance();
    }
    
    // getters and setters
    function get_external_grade_id()
    {
    	return $this->get_default_property(self :: PROPERTY_EXTERNAL_GRADE_ID);
    }
    
    function set_external_grade_id($external_grade_id)
    {
    	$this->set_default_property(self :: PROPERTY_EXTERNAL_GRADE_ID, $external_grade_id);
    }
    
    function get_titel()
    {
    	return $this->get_default_property(self :: PROPERTY_TITEL);
    }
    
    function set_titel($titel)
    {
    	$this->set_default_property(self :: PROPERTY_TITEL, $titel);
    }
    
    function get_description()
    {
    	return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }
    
    function set_description($description)
    {
    	$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }
    
	function get_format_id()
    {
    	return $this->get_default_property(self :: PROPERTY_FORMAT_ID);
    }
    
    function set_format_id($format_id)
    {
    	$this->set_default_property(self :: PROPERTY_FORMAT_ID, $format_id);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>