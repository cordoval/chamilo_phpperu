<?php

class DynamicFormElementBuilderForm extends FormValidator
{
	private $user;
	private $form_type;
	private $element;
	
	const TYPE_CREATE = 0;
	const TYPE_EDIT = 1;
	
	function DynamicFormElementBuilderForm($form_type, $element, $action, $user)
	{
		parent :: FormValidator('dynamic_form_element', 'post', $action);
		$this->user = $user;
		$this->form_type = $form_type;
		$this->element = $element;
		
		if($form_type == self :: TYPE_CREATE)
		{
			$this->build_creation_form();
		}
		else
		{
			$this->build_edit_form();
		}
	}
	
	function build_creation_form()
	{
		$this->build_basic_form();
		
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
	}
	
	function build_edit_form()
	{
		$this->build_basic_form();
		
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $this->setDefaults();
	}
	
	function build_basic_form()
	{
		$this->addElement('text', DynamicFormElement :: PROPERTY_NAME, Translation :: get('Name'));
		$this->addRule(DynamicFormElement :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
		
		$this->addElement('checkbox', DynamicFormElement :: PROPERTY_REQUIRED, Translation :: get('Required'));
	}
	
	function create_dynamic_form_element()
	{
		$values = $this->exportValues();
		$element = $this->element;
		
		$element->set_name($values[DynamicFormElement :: PROPERTY_NAME]);
		$element->set_required($values[DynamicFormElement :: PROPERTY_REQUIRED]);
		return $element->create();
	}
	
	function update_dynamic_form_element()
	{
		$values = $this->exportValues();
		$element = $this->element;
		
		$element->set_name($values[DynamicFormElement :: PROPERTY_NAME]);
		$element->set_required($values[DynamicFormElement :: PROPERTY_REQUIRED]);
		return $element->update();
	}
	
	function setDefaults($parameters = array())
	{
		$parameters[DynamicFormElement :: PROPERTY_NAME] = $this->element->get_name();
		$parameters[DynamicFormElement :: PROPERTY_REQUIRED] = $this->element->get_required();
		parent :: setDefaults($parameters);
	}
}

?>