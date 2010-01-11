<?php
/**
 * $Id: dynamic_form_manager_component.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package application.common.dynamic_form_manager
 * @author Sven Vanpoucke
 */

class DynamicFormManagerComponent extends SubManagerComponent
{
	function get_add_element_url()
    {
    	return $this->get_parent()->get_add_element_url();
    }
    
    function get_update_element_url($element)
    {
    	return $this->get_parent()->get_add_element_url();
    }
    
    function delete_element_url($element)
    {
    	return $this->get_parent()->get_add_element_url();
    }
    
    function get_form()
    {
    	return $this->get_parent()->get_form();
    }
}
?>