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
    const PROPERTY_DISPLAY_ORDER = 'display_order';
    
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
    
	function get_display_order()
    {
    	return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
    }
    
    function set_display_order($display_order)
    {
    	$this->set_default_property(self :: PROPERTY_DISPLAY_ORDER, $display_order);
    }

    /**
     * Get the default properties of all user course categories.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_DYNAMIC_FORM_ELEMENT_ID,
        		self :: PROPERTY_NAME, self :: PROPERTY_DISPLAY_ORDER));
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
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
    
	function create()
    {
    	$this->set_display_order(AdminDataManager :: get_instance()->select_next_dynamic_form_element_option_order($this->get_dynamic_form_element_id()));
    	return parent :: create();
    }
}
?>