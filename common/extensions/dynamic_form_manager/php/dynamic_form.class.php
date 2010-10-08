<?php
/**
 * $Id: dynamic_form.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package application.common.dynamic_form_manager
 * @author Sven Vanpoucke
 */

class DynamicForm extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_NAME = 'name';
    const PROPERTY_APPLICATION = 'application';
    
    private $elements;
    
    function DynamicForm($defaultProperties)
    {
    	parent :: DataClass($defaultProperties);
    	//$this->elements = array();
    }
    
    function get_name()
    {
    	return $this->get_default_property(self :: PROPERTY_NAME);
    }
    
    function set_name($name)
    {
    	$this->set_default_property(self :: PROPERTY_NAME, $name);
    }
    
	function get_application()
    {
    	return $this->get_default_property(self :: PROPERTY_APPLICATION);
    }
    
    function set_application($application)
    {
    	$this->set_default_property(self :: PROPERTY_APPLICATION, $application);
    }
    
    function get_elements()
    {
    	if(!$this->elements)
    		$this->load_elements();
    		
    	return $this->elements;
    }
    
    function get_element($index)
    {
    	return $this->elements[$index];
    }
    
    function set_elements($elements)
    {
    	$this->elements = $elements;
    }
    
    function add_elements($elements)
    {
    	if(!is_array($elements))
    	{
    		$elements = array($elements);
    	}
    		
    	foreach($elements as $element)
    	{
    		$this->elements[] = $element;
    	}
    }
    
    function load_elements()
    {
    	require_once dirname(__FILE__) . '/dynamic_form_element.class.php';
    	$condition = new EqualityCondition(DynamicFormElement :: PROPERTY_DYNAMIC_FORM_ID, $this->get_id());
    	$elements = AdminDataManager :: get_instance()->retrieve_dynamic_form_elements($condition);
    	$this->set_elements($elements->as_array());
    	
    	return $this->elements;
    }

    /**
     * Get the default properties of all user course categories.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_NAME, self :: PROPERTY_APPLICATION));
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