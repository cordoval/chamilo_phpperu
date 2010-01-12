<?php
/**
 * $Id: dynamic_form_manager.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package application.common.dynamic_form_manager
 * @author Sven Vanpoucke
 */

require_once dirname(__FILE__) . '/dynamic_form_manager_component.class.php';
require_once dirname(__FILE__) . '/dynamic_form.class.php';
require_once dirname(__FILE__) . '/component/dynamic_form_element_browser/dynamic_form_element_browser_table.class.php';

class DynamicFormManager extends SubManager
{
    const PARAM_DYNAMIC_FORM_ACTION = 'dynfo_action';
    const PARAM_DYNAMIC_FORM_ID = 'dynfo_id';
    const PARAM_DYNAMIC_FORM_ELEMENT_ID = 'dynfo_el_id';
    const PARAM_DYNAMIC_FORM_ELEMENT_TYPE = 'dynfo_el_type';
    const PARAM_DELETE_FORM_ELEMENETS = 'delete_elements';
    
    const ACTION_BUILD_DYNAMIC_FORM = 'builder';
    const ACTION_VIEW_DYNAMIC_FORM = 'viewer';
    const ACTION_EXECUTE_DYNAMIC_FORM = 'executer';
    const ACTION_ADD_FORM_ELEMENT = 'add_element';
    const ACTION_DELETE_FORM_ELEMENT = 'delete_element';
    const ACTION_UPDATE_FORM_ELEMENT = 'update_element';

    const TYPE_BUILDER = 0;
    const TYPE_VIEWER = 1;
    const TYPE_EXECUTER = 2;
    
    private $form;
    private $type;
    private $target_user_id;
    
    function DynamicFormManager($parent, $application, $name, $type)
    {
        parent :: __construct($parent);
        
        $dynamic_form_action = Request :: get(self :: PARAM_DYNAMIC_FORM_ACTION);
        if ($dynamic_form_action)
        {
            $this->set_parameter(self :: PARAM_DYNAMIC_FORM_ACTION, $dynamic_form_action);
        }
        
        $this->type = $type;
        
        $this->set_form($this->retrieve_form($application, $name));
        
        $this->parse_input_from_table();
    }

    function run()
    {
        $dynamic_form_action = $this->get_parameter(self :: PARAM_DYNAMIC_FORM_ACTION);
        
        switch ($dynamic_form_action)
        {
            case self :: ACTION_BUILD_DYNAMIC_FORM :
                $component = DynamicFormManagerComponent :: factory('Builder', $this);
                break;
            case self :: ACTION_VIEW_DYNAMIC_FORM :
                $component = DynamicFormManagerComponent :: factory('Viewer', $this);
                break;
            case self :: ACTION_ADD_FORM_ELEMENT :
                $component = DynamicFormManagerComponent :: factory('AddElement', $this);
                break;
            case self :: ACTION_UPDATE_FORM_ELEMENT :
                $component = DynamicFormManagerComponent :: factory('UpdateElement', $this);
                break;
            case self :: ACTION_DELETE_FORM_ELEMENT :
                $component = DynamicFormManagerComponent :: factory('DeleteElement', $this);
                break;
            case self :: ACTION_EXECUTE_DYNAMIC_FORM :
            	$component = DynamicFormManagerComponent :: factory('Executer', $this);
                break;
            default :
            	switch($this->type)
            	{
            		case self :: TYPE_VIEWER:
            			$component = DynamicFormManagerComponent :: factory('Viewer', $this);
            			break;
            		case self :: TYPE_BUILDER:
            			$component = DynamicFormManagerComponent :: factory('Builder', $this);
            			break;
            		case self :: TYPE_EXECUTER:
            			$component = DynamicFormManagerComponent :: factory('Executer', $this);
            			break;
            	}
                break;
        }
        
        $component->run();
    }
    
    function parse_input_from_table()
    {
    	if (isset($_POST['action']))
        {
            $selected_ids = $_POST[DynamicFormElementBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
            if (empty($selected_ids))
            {
                $selected_ids = array();
            }
            elseif (! is_array($selected_ids))
            {
                $selected_ids = array($selected_ids);
            }

            switch ($_POST['action'])
            {
                case self :: PARAM_DELETE_FORM_ELEMENETS :
                    $this->set_parameter(self :: PARAM_DYNAMIC_FORM_ACTION, self :: ACTION_DELETE_FORM_ELEMENT);
                    Request :: set_get(self :: PARAM_DYNAMIC_FORM_ELEMENT_ID, $selected_ids);
                    break;
            }
        }
    }
    
    function get_form()
    {
    	return $this->form;
    }
    
    function set_form($form)
    {
    	$this->form = $form;
    }
    
    function set_target_user_id($target_user_id)
    {
    	$this->target_user_id = $target_user_id;
    }
    
    function get_target_user_id($target_user_id)
    {
    	return $this->target_user_id;
    }
    
    function get_application_component_path()
    {
        return Path :: get_application_library_path() . 'dynamic_form_manager/component/';
    }

    function get_add_element_url()
    {
    	return $this->get_url(array(self :: PARAM_DYNAMIC_FORM_ACTION => self :: ACTION_ADD_FORM_ELEMENT));
    }
    
    function get_update_element_url($element)
    {
    	return $this->get_url(array(self :: PARAM_DYNAMIC_FORM_ACTION => self :: ACTION_UPDATE_FORM_ELEMENT,
    								self :: PARAM_DYNAMIC_FORM_ELEMENT_ID => $element->get_id()));
    }
    
    function get_delete_element_url($element)
    {
    	return $this->get_url(array(self :: PARAM_DYNAMIC_FORM_ACTION => self :: ACTION_DELETE_FORM_ELEMENT,
    								self :: PARAM_DYNAMIC_FORM_ELEMENT_ID => $element->get_id()));
    }
    
    private function retrieve_form($application, $name)
    {
    	$conditions = array();
    	$conditions[] = new EqualityCondition(DynamicForm :: PROPERTY_APPLICATION, $application);
    	$conditions[] = new EqualityCondition(DynamicForm :: PROPERTY_NAME, $name);
    	$condition = new AndCondition($conditions);
    	$form = AdminDataManager :: get_instance()->retrieve_dynamic_forms($condition)->next_result();
    	
    	if(!$form)
    	{
    		$form = new DynamicForm();
    		$form->set_application($application);
    		$form->set_name($name);
    		$form->create();
    	}
    	
    	return $form;
    }
    
	function get_dynamic_form_title()
	{
		return $this->get_parent()->get_dynamic_form_title();
	}
}
?>