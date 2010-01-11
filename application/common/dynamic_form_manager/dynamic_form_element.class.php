<?php
/**
 * $Id: dynamic_form_element.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package application.common.dynamic_form_manager
 * @author Sven Vanpoucke
 */

class DynamicFormElement extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_DYNAMIC_FORM_ID = 'dynamic_form_id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_REQUIRED = 'required';
    const PROPERTY_DISPLAY_ORDER = 'display_order';
    
    const TYPE_TEXTBOX = 1;
    const TYPE_HTMLEDITOR = 2;
    const TYPE_RADIO_BUTTONS = 3;
    const TYPE_CHECKBOX = 4;
    const TYPE_SELECT_BOX = 5;
    
    private $options;
    
	function DynamicForm($defaultProperties)
    {
    	parent :: DataClass($defaultProperties);
    	$this->options = array();
    }
    
 	function get_dynamic_form_id()
    {
    	return $this->get_default_property(self :: PROPERTY_DYNAMIC_FORM_ID);
    }
    
    function set_dynamic_form_id($dynamic_form_id)
    {
    	$this->set_default_property(self :: PROPERTY_DYNAMIC_FORM_ID, $dynamic_form_id);
    }
    
    function get_name()
    {
    	return $this->get_default_property(self :: PROPERTY_NAME);
    }
    
    function set_name($name)
    {
    	$this->set_default_property(self :: PROPERTY_NAME, $name);
    }
    
	function get_type()
    {
    	return $this->get_default_property(self :: PROPERTY_TYPE);
    }
    
    function set_type($type)
    {
    	$this->set_default_property(self :: PROPERTY_TYPE, $type);
    }
    
	function get_required()
    {
    	return $this->get_default_property(self :: PROPERTY_REQUIRED);
    }
    
    function set_required($required)
    {
    	$this->set_default_property(self :: PROPERTY_REQUIRED, $required);
    }
    
	function get_display_order()
    {
    	return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
    }
    
    function set_display_order($display_order)
    {
    	$this->set_default_property(self :: PROPERTY_DISPLAY_ORDER, $display_order);
    }
    
	function get_options()
    {
    	return $this->options;
    }
    
    function get_option($index)
    {
    	return $this->options[$index];
    }
    
    function set_options($options)
    {
    	$this->options = $options;
    }
    
    function add_options($options)
    {
    	if(!is_array($options))
    	{
    		$options = array($options);
    	}
    		
    	foreach($options as $option)
    	{
    		$this->options[] = $option;
    	}
    }

	function load_options()
    {
    	$condition = new EqualityCondition(DynamicFormElementOption :: PROPERTY_DYNAMIC_FORM_ELEMENT_ID, $this->get_id());
    	$options = AdminDataManager :: get_instance()->retrieve_dynamic_form_element_options($condition);
    	$this->set_options($options->as_array());
    	
    	return $this->get_options();
    }
    
    /**
     * Get the default properties of all user course categories.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_DYNAMIC_FORM_ID,
        		self :: PROPERTY_NAME, self :: PROPERTY_TYPE, self :: PROPERTY_REQUIRED,
        		self :: PROPERTY_DISPLAY_ORDER));
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
    
    static function get_types()
    {
    	return array(
    		Translation :: get('Textbox') => self :: TYPE_TEXTBOX,
    		Translation :: get('HtmlEditor') => self :: TYPE_HTMLEDITOR,
    		Translation :: get('RadioButtons') => self :: TYPE_RADIO_BUTTONS,
    		Translation :: get('Checkbox') => self :: TYPE_CHECKBOX,
    		Translation :: get('SelectBox') => self :: TYPE_SELECT_BOX
    	);
    }
    
    static function get_type_name($type)
    {
    	switch($type)
    	{
    		case self :: TYPE_TEXTBOX:
    			return Translation :: get('Textbox');
    		case self :: TYPE_HTMLEDITOR:
    			return Translation :: get('HtmlEditor');
    		case self :: TYPE_RADIO_BUTTONS:
    			return Translation :: get('RadioButtons');
    		case self :: TYPE_CHECKBOX:
    			return Translation :: get('Checkbox');
    		case self :: TYPE_SELECT_BOX:
    			return Translation :: get('SelectBox');
    	}
    }
}
?>