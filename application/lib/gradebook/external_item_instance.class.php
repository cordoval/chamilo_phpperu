<?php
/*
 * @author Ben Vanmassenhove
 */
class ExternalItemInstance extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'external_item_instance';
    /*
     * ExternalItemInstance porperties
     */
    const PROPERTY_EXTERNAL_ITEM_ID = 'external_item_id';
    const PROPERTY_EVALUATION_ID = 'evaluation_id';
	/**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
    	return parent :: get_default_property_names(array(self :: PROPERTY_EXTERNAL_ITEM_ID, self :: PROPERTY_EVALUATION_ID));
    }
    
    function get_data_manager()
    {
    	return GradebookDataManager :: get_instance();
    }
    
    // getters and setters
    
    function get_external_item_id()
    {
    	$this->get_default_property(self :: PROPERTY_EXTERNAL_ITEM_ID);
    }
    
    function set_external_item_id($external_item_id)
    {
    	$this->set_default_property(self :: PROPERTY_EXTERNAL_ITEM_ID, $external_item_id);
    }
    
    function get_evaluation_id()
    {
    	$this->get_default_property(self :: PROPERTY_EVALUATION_ID);
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