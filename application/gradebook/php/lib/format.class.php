<?php
/*
 * @author Johan Janssens
 */
class Format extends DataClass
{
	const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'format';
    
    /**
     * Format properties
     */
    const PROPERTY_TITLE = 'title';
    const PROPERTY_ACTIVE = 'active';
    /*
     * Possible active values
     */
    const EVALUATION_FORMAT_NON_ACTIVE = 0;
    const EVALUATION_FORMAT_ACTIVE = 1;
    
	static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_TITLE, self :: PROPERTY_ACTIVE));
    }

    function get_data_manager()
    {
        return GradebookDataManager :: get_instance();
    }
    // getters and setters
	/**
    * Sets the title of this Format.
    * @param title.
    */	
    function set_title($title)
    {
    	$this->set_default_property(self :: PROPERTY_TITLE, $title);
    }  
	/**
    * Returns the title of this Format.
    * @return title.
    */	
    function get_title()
    {
    	return $this->get_default_property(self :: PROPERTY_TITLE);
    }

	/**
    * Sets the active property of this Format.
    */	
    function set_active($active)
    {
    	$this->set_default_property(self :: PROPERTY_ACTIVE, $active);
    }  
	/**
    * Returns the active property of this Format.
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