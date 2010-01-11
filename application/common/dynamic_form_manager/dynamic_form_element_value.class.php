<?php
/**
 * $Id: dynamic_form_element_value.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package application.common.dynamic_form_manager
 * @author Sven Vanpoucke
 */

class DynamicFormElementValue extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_DYNAMIC_FORM_ELEMENT_ID = 'dynamic_form_element_id';
    const PROPERTY_VALUE = 'value';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_TIME = 'time';
    
 	function get_dynamic_form_element_id()
    {
    	return $this->get_default_property(self :: PROPERTY_DYNAMIC_FORM_ELEMENT_ID);
    }
    
    function set_dynamic_form_element_id($dynamic_form_element_id)
    {
    	$this->set_default_property(self :: PROPERTY_DYNAMIC_FORM_ELEMENT_ID, $dynamic_form_element_id);
    }
    
    function get_value()
    {
    	return $this->get_default_property(self :: PROPERTY_VALUE);
    }
    
    function set_value($value)
    {
    	$this->set_default_property(self :: PROPERTY_VALUE, $value);
    }
    
	function get_user_id()
    {
    	return $this->get_default_property(self :: PROPERTY_USER_ID);
    }
    
    function set_user_id($user_id)
    {
    	$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }
    
	function get_time()
    {
    	return $this->get_default_property(self :: PROPERTY_TIME);
    }
    
    function set_time($time)
    {
    	$this->set_default_property(self :: PROPERTY_TIME, $time);
    }

    /**
     * Get the default properties of all user course categories.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_DYNAMIC_FORM_ELEMENT_ID,
        		self :: PROPERTY_VALUE, self :: PROPERTY_USER_ID, self :: PROPERTY_TIME));
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
}
?>