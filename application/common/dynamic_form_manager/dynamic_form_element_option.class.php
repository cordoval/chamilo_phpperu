<?php
/**
 * $Id: dynamic_form_element_option.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package application.common.dynamic_form_manager
 * @author Sven Vanpoucke
 */

class DynamicFormElementOption extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_DYNAMIC_FORM_ELEMENT_ID = 'dynamic_form_element_id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_ORDER = 'order';
    
 	function get_dynamic_form_element_id()
    {
    	return $this->get_default_property(self :: PROPERTY_DYNAMIC_FORM_ELEMENT_ID);
    }
    
    function set_dynamic_form_element_id($dynamic_form_element_id)
    {
    	$this->set_default_property(self :: PROPERTY_DYNAMIC_FORM_ELEMENT_ID, $dynamic_form_element_id);
    }
    
    function get_name()
    {
    	return $this->get_default_property(self :: PROPERTY_NAME);
    }
    
    function set_name($name)
    {
    	$this->set_default_property(self :: PROPERTY_NAME, $name);
    }
    
	function get_order()
    {
    	return $this->get_default_property(self :: PROPERTY_ORDER);
    }
    
    function set_order($order)
    {
    	$this->set_default_property(self :: PROPERTY_ORDER, $order);
    }

    /**
     * Get the default properties of all user course categories.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_DYNAMIC_FORM_ELEMENT_ID,
        		self :: PROPERTY_NAME, self :: PROPERTY_ORDER));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return AdminDataManager :: get_instance();
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>