<?php
/*
 * @author Ben Vanmassenhove
 */
class GradebookInternalEvaluation extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'internal_evaluation';
    /**
     * GradebookEvaluation properties
     */
    const PROPERTY_APPLICATION = 'application';
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_FORMAT_ID = 'format_id';
    const PROPERTY_CALCULATED = 'calculated';
    
    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_defualt_property_names()
    {
    	return parent :: get_defualt_property_names(array(self :: PROPERTY_APPLICATION, self :: PROPERTY_PUBLICATION));
    }
    
    function get_data_manager()
    {
    	return GradebookDataManager :: get_instance();
    }
    
    // getters and setters

    function get_application()
    {
    	return $this->get_default_property(self :: PROPERTY_APPLICATION);
    }
    
    function set_application($application)
    {
    	$this->set_default_property(self :: PROPERTY_APPLICATION, $application);
    }

    function get_publication_id()
    {
    	return $this->get_default_property(self :: PROPERTY_PUBLICATION_ID);
    }
    
    function set_publication_id($publication_id)
    {
    	$this->set_default_property(self :: PROPERTY_PUBLICATION_ID, $publication_ID);
    }

    function get_format_id()
    {
    	return $this->get_default_property(self :: PROPERTY_FORMAT_ID);
    }
    
    function set_format_id($format_id)
    {
    	$this->set_default_property(self :: PROPERTY_FORMAT_ID, $format_id);
    }

    function get_calculated()
    {
    	return $this->get_default_property(self :: PROPERTY_CALCULATED);
    }
    
    function set_calculated($calculated)
    {
    	$this->set_default_property(self :: PROPERTY_CALCULATED, $calculated);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>