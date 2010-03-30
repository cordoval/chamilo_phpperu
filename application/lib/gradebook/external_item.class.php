<?php
/*
 * @author Ben Vanmassenhove
 */
class ExternalItem extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'external_item';
    /*
     * GradebookEvaluationGrades properties
     */
    const PROPERTY_TITEL = 'titel';
    const PROPERTY_DESCRIPTION = 'description';
	/**
     * Get the default properties
     * @return array The property names.
     */
    static function get_defualt_property_names()
    {
    	return parent :: get_defualt_property_names(array(self :: PROPERTY_TITEL, self :: PROPERTY_DESCRIPTION));
    }
    
    function get_data_manager()
    {
    	return GradebookDataManager :: get_instance();
    }
    
    // getters and setters
    
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

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>