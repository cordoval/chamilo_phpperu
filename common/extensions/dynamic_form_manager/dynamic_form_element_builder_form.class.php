<?php

require_once (dirname(__FILE__) . '/dynamic_form_element.class.php');
require_once (dirname(__FILE__) . '/dynamic_form_element_option.class.php');

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
	}
	
	function build_basic_form()
	{
		$this->addElement('category', $this->element->get_type_name($this->element->get_type()));
		
		$this->addElement('text', DynamicFormElement :: PROPERTY_NAME, Translation :: get('Name'));
		$this->addRule(DynamicFormElement :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
		
		$this->addElement('checkbox', DynamicFormElement :: PROPERTY_REQUIRED, Translation :: get('Required'));
		
		$this->addElement('category');
		
		if($this->element->get_type() >= DynamicFormElement :: TYPE_RADIO_BUTTONS)
		{
			$this->build_options();
		}
		
		$this->setDefaults();
	}
	
	function build_options()
	{
		$this->addElement('category', Translation :: get('Options'));
		
		if (! $this->isSubmitted())
        {
            unset($_SESSION['mc_number_of_options']);
            unset($_SESSION['mc_skip_options']);
            
            if(count($this->element->get_options()) > 0)
            {
            	$_SESSION['mc_number_of_options'] = count($this->element->get_options());
            }
        }
        
        if (! isset($_SESSION['mc_number_of_options']))
        {
            $_SESSION['mc_number_of_options'] = 3;
        }
        
        if (! isset($_SESSION['mc_skip_options']))
        {
            $_SESSION['mc_skip_options'] = array();
        }
        
        if (isset($_POST['add']))
        {
            $_SESSION['mc_number_of_options'] = $_SESSION['mc_number_of_options'] + 1;
        }
        if (isset($_POST['remove']))
        {
            $indexes = array_keys($_POST['remove']);
            $_SESSION['mc_skip_options'][] = $indexes[0];
        }
        
        $number_of_options = intval($_SESSION['mc_number_of_options']);
        
        for($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            if (! in_array($option_number, $_SESSION['mc_skip_options']))
            {
                $group = array();
                $group[] = $this->createElement('text', 'option_' . DynamicFormElementOption :: PROPERTY_NAME . '[' . $option_number . ']', Translation :: get('Name'), array("size" => "50"));
                if ($number_of_options - count($_SESSION['mc_skip_options']) > 1)
                {
                    $group[] = $this->createElement('image', 'remove[' . $option_number . ']', Theme :: get_common_image_path() . 'action_list_remove.png', array('style="border: 0px;"'));
                }
                $this->addGroup($group, 'option_' . DynamicFormElementOption :: PROPERTY_NAME . '[' . $option_number . ']', Translation :: get('OptionName'), '', false);
                $this->addRule('option_' . DynamicFormElementOption :: PROPERTY_NAME . '[' . $option_number . ']', Translation :: get('ThisFieldIsRequired'), 'required');
            }
        }
        
        $this->addElement('image', 'add[]', Theme :: get_common_image_path() . 'action_list_add.png', array('style="border: 0px;"'));
		
		$this->addElement('category');
	}
	
	function validate()
    {
        if (isset($_POST['add']) || isset($_POST['remove']))
        {
            return false;
        }
        return parent :: validate();
    }
	
	function create_dynamic_form_element()
	{
		$values = $this->exportValues();
		$element = $this->element;
		
		$element->set_name($values[DynamicFormElement :: PROPERTY_NAME]);
		$element->set_required($values[DynamicFormElement :: PROPERTY_REQUIRED] ? 1 : 0);
		$succes = $element->create();
		
		if(!$succes)
			return false;
		
		foreach($values['option_' . DynamicFormElementOption :: PROPERTY_NAME] as $option)
		{
			$element_option = new DynamicFormElementOption();
			$element_option->set_dynamic_form_element_id($element->get_id());
			$element_option->set_name($option);
			$succes &= $element_option->create();
		}
			
		return $succes;
	}
	
	function update_dynamic_form_element()
	{
		$values = $this->exportValues();
		$element = $this->element;
		
		$element->set_name($values[DynamicFormElement :: PROPERTY_NAME]);
		$element->set_required($values[DynamicFormElement :: PROPERTY_REQUIRED] ? 1 : 0);
		$succes = $element->update();
		
		if(!$succes)
			return false;
		
		AdminDataManager :: get_instance()->delete_all_options_from_form_element($element->get_id());
			
		foreach($values['option_' . DynamicFormElementOption :: PROPERTY_NAME] as $option)
		{
			$element_option = new DynamicFormElementOption();
			$element_option->set_dynamic_form_element_id($element->get_id());
			$element_option->set_name($option);
			$succes &= $element_option->create();
		}
			
		return $succes;
	}
	
	function setDefaults($parameters = array())
	{
		$parameters[DynamicFormElement :: PROPERTY_NAME] = $this->element->get_name();
		$parameters[DynamicFormElement :: PROPERTY_REQUIRED] = $this->element->get_required();
		
		if (! $this->isSubmitted())
        {
            $element = $this->element;
            if (! is_null($element))
            {
                $options = $element->get_options(); 
                
                foreach ($options as $index => $option)
                {
                    $parameters['option_' . DynamicFormElementOption :: PROPERTY_NAME][$index] = $option->get_name();
                }
            }
        }
		
		parent :: setDefaults($parameters);
	}
}

?>