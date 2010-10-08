<?php

require_once (dirname(__FILE__) . '/dynamic_form_element.class.php');
require_once (dirname(__FILE__) . '/dynamic_form_element_option.class.php');
require_once (dirname(__FILE__) . '/dynamic_form_element_value.class.php');

class DynamicFormExecuteForm extends FormValidator
{
	private $user;
	private $form;
	private $title;
	
	function DynamicFormExecuteForm($form, $action, $user, $title)
	{
		parent :: FormValidator('dynamic_form_values', 'post', $action);
		$this->user = $user;
		$this->form = $form;
		$this->title = $title;
		
		$this->build_basic_form();
	}
	
	function build_basic_form()
	{
		//$this->addElement('category', $this->title);
		
		$elements = $this->form->get_elements();
		foreach($elements as $element)
		{
			switch($element->get_type())
			{
				case DynamicFormElement :: TYPE_TEXTBOX:
					$this->build_text_box($element);
					break;
				case DynamicFormElement :: TYPE_HTMLEDITOR:
					$this->build_html_editor($element);
					break;
				case DynamicFormElement :: TYPE_CHECKBOX:
					$this->build_checkbox($element);
					break;
				case DynamicFormElement :: TYPE_RADIO_BUTTONS:
					$this->build_radio_buttons($element);
					break;
				case DynamicFormElement :: TYPE_SELECT_BOX:
					$this->build_select_box($element);
					break;
			}
		}
		
		//$this->addElement('category');
		
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $this->setDefaults();
	}
	
	function update_values()
	{
		$values = $this->exportValues(); 
		$succes = AdminDataManager :: get_instance()->delete_dynamic_form_element_values_from_form($this->form->get_id());
		
		if(!$succes)
			return false;
		
		foreach($values['element'] as $element_id => $value)
		{
			$element_value = new DynamicFormElementValue();
			$element_value->set_dynamic_form_element_id($element_id);
			$element_value->set_user_id($this->user->get_id());
			
			if(is_array($value))
			{
				$value = $value[$element_id];
			}
			
			$element_value->set_value($value);
			$succes &= $element_value->create();
		}
		
		return $succes;
	}

	function build_text_box($element)
	{
		$return = $this->addElement('text', 'element[' . $element->get_id() . ']', $element->get_name());
		if($element->get_required())
		{
			$this->addRule('element[' . $element->get_id() . ']', Translation :: get('ThisFieldIsRequired'), 'required');
		}
		
		return $return;
	}

	function build_html_editor($element)
	{
		return $this->add_html_editor('element[' . $element->get_id() . ']', $element->get_name(), $element->get_required());
	}
	
	function build_checkbox($element)
	{
		$return = $this->addElement('checkbox', 'element[' . $element->get_id() . ']', $element->get_name());
		
		if($element->get_required())
		{
			$this->addRule('element[' . $element->get_id() . ']', Translation :: get('ThisFieldIsRequired'), 'required');
		}
		
		return $return;
	}
	
	function build_radio_buttons($element)
	{
		$options = $element->get_options();
		
		$group = array();
		
		foreach($options as $index => $option)
		{
			if($index < count($options) - 1)
				$extra = '<br />';
			else
				$extra = '';
				
			$group[] = $this->createElement('radio', $element->get_id(), null, $option->get_name() . $extra, $option->get_id());
		}
		
		$return = $this->addGroup($group, 'element[' . $element->get_id() . ']', $element->get_name(), '');
		
		if($element->get_required())
		{
			$this->addRule('element[' . $element->get_id() . ']', Translation :: get('ThisFieldIsRequired'), 'required');
		}
		
		return $return;
	}
	
	function build_select_box($element)
	{
		$options = $element->get_options();
		
		foreach($options as $option)
		{
			$new_options[$option->get_id()] = $option->get_name();
		}
		
		$return = $this->addElement('select', 'element[' . $element->get_id() . ']', $element->get_name(), $new_options);
		
		if($element->get_required())
		{
			$this->addRule('element[' . $element->get_id() . ']', Translation :: get('ThisFieldIsRequired'), 'required');
		}
		
		return $return;
	}
	
	function setDefaults($parameters = array())
	{
		$subcondition = new EqualityCondition(DynamicFormElement :: PROPERTY_DYNAMIC_FORM_ID, $this->form->get_id());
    	$subselect = new SubselectCondition(DynamicFormElementValue :: PROPERTY_DYNAMIC_FORM_ELEMENT_ID, DynamicFormElement :: PROPERTY_ID, 
    										DynamicFormElement :: get_table_name(), $subcondition);
    	
    	$values = AdminDataManager :: get_instance()->retrieve_dynamic_form_element_values($subselect);
    	
    	while($value = $values->next_result())
    	{
    		$element = AdminDataManager :: get_instance()->retrieve_dynamic_form_elements(
    						new EqualityCondition(DynamicFormElement :: PROPERTY_ID, $value->get_dynamic_form_element_id()))->next_result();
				
			if($element->get_type() == DynamicFormElement :: TYPE_RADIO_BUTTONS)
			{
				$parameters['element[' . $value->get_dynamic_form_element_id() . '][' . $value->get_dynamic_form_element_id() . ']'] = $value->get_value();
			}
			else
			{
    			$parameters['element[' . $value->get_dynamic_form_element_id() . ']'] = $value->get_value();
			}
    	}

    	parent :: setDefaults($parameters);
	}
}

?>