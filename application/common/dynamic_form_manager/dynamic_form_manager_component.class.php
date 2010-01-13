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
    	return $this->get_parent()->get_update_element_url($element);
    }
    
    function get_delete_element_url($element)
    {
    	return $this->get_parent()->get_delete_element_url($element);
    }
    
    function get_form()
    {
    	return $this->get_parent()->get_form();
    }
    
	function get_target_user_id($target_user_id)
    {
    	return $this->get_parent()->get_target_user_id($target_user_id);
    }
    
	function get_dynamic_form_title()
	{
		return $this->get_parent()->get_dynamic_form_title();
	}
}
?>