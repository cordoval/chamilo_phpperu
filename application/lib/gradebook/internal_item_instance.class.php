<?php
/*
 * @author Ben Vanmassenhove
 */
class InternalItemInstance extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'internal_item_instance';
    /*
     * ExternalItemInstance porperties
     */
    const PROPERTY_INTERNAL_ITEM_ID = 'internal_item_id';
    const PROPERTY_EVALUATION_ID = 'evaluation_id';
	/**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
    	return parent :: get_default_property_names(array(self :: PROPERTY_INTERNAL_ITEM_ID, self :: PROPERTY_EVALUATION_ID));
    }
    
    function get_data_manager()
    {
    	return GradebookDataManager :: get_instance();
    }
    
    // getters and setters
    
    function get_internal_item_id()
    {
    	return $this->get_default_property(self :: PROPERTY_INTERNAL_ITEM_ID);
    }
    
    function set_internal_item_id($internal_item_id)
    {
    	$this->set_default_property(self :: PROPERTY_INTERNAL_ITEM_ID, $internal_item_id);
    }
    
    function get_evaluation_id()
    {
    	return $this->get_default_property(self :: PROPERTY_EVALUATION_ID);
    }
    
    function set_evaluation_id($evaluation_id)
    {
    	$this->set_default_property(self :: PROPERTY_EVALUATION_ID, $evaluation_id);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>