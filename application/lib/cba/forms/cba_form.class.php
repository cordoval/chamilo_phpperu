<?php
require_once dirname(__FILE__) . '/../competency.class.php';
require_once dirname(__FILE__) . '/../indicator.class.php';
require_once dirname(__FILE__) . '/../criteria.class.php';
/**
 * This class describes a CbaForm object
 * 
 * @author Nick Van Loocke
 **/
class CbaForm extends FormValidator
{
	
	const TYPE_CREATOR_COMPETENCY = 1;
	const TYPE_CREATOR_INDICATOR = 2;
	const TYPE_CREATOR_CRITERIA = 3;
	
	const TYPE_EDITOR_COMPETENCY = 4;
	const TYPE_EDITOR_INDICATOR = 5;
	const TYPE_EDITOR_CRITERIA = 6;

	private $cba_type;
	private $user;

    function CbaForm($form_type, $cba_type, $action, $user)
    {
    	parent :: __construct('cba_settings', 'post', $action);

    	$this->cba_type = $cba_type;
    	$this->user = $user;
		$this->form_type = $form_type;

		if ($this->form_type == self :: TYPE_CREATOR_COMPETENCY)
		{
			$this->build_creator_competency_form();
			$this->setCompetencyDefaults();
		}
    	elseif ($this->form_type == self :: TYPE_CREATOR_INDICATOR)
		{
			$this->build_creator_indicator_form();
			$this->setIndicatorDefaults();
		}
    	elseif ($this->form_type == self :: TYPE_CREATOR_CRITERIA)
		{
			$this->build_creator_criteria_form();
			$this->setCriteriaDefaults();
		}
    	elseif ($this->form_type == self :: TYPE_EDITOR_COMPETENCY)
		{
			$this->build_editor_competency_form();
			$this->setCompetencyDefaults();
		}
   	 	elseif ($this->form_type == self :: TYPE_EDITOR_INDICATOR)
		{
			$this->build_editor_indicator_form();
			$this->setIndicatorDefaults();
		}
    	elseif ($this->form_type == self :: TYPE_EDITOR_CRITERIA)
		{
			$this->build_editor_criteria_form();
			$this->setCriteriaDefaults();
		}

    }
    
    // Creator forms
    
	function build_creator_competency_form()
    {
    	$this->addElement('text', Competency :: PROPERTY_TITLE, Translation :: get('Title'));
		$this->addRule(Competency :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->add_html_editor(Competency :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
		$this->addRule(Competency :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');
    	
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
    
	function build_creator_indicator_form()
    {
    	$this->addElement('text', Indicator :: PROPERTY_TITLE, Translation :: get('Title'));
		$this->addRule(Indicator :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->add_html_editor(Indicator :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
		$this->addRule(Indicator :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');
    	
    	
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
    
	function build_creator_criteria_form()
    {
    	$this->addElement('text', Criteria :: PROPERTY_TITLE, Translation :: get('Title'));
		$this->addRule(Criteria :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->add_html_editor(Criteria :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
		$this->addRule(Criteria :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');
    	
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
    
    
    // Editor forms
    
	function build_editor_competency_form()
    {
    	$this->addElement('text', Competency :: PROPERTY_TITLE, Translation :: get('Title'));
		$this->addRule(Competency :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->add_html_editor(Competency :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
		$this->addRule(Competency :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');
    	
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
    
	function build_editor_indicator_form()
    {
    	$this->addElement('text', Indicator :: PROPERTY_TITLE, Translation :: get('Title'));
		$this->addRule(Indicator :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->add_html_editor(Indicator :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
		$this->addRule(Indicator :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');
    	
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
    
	function build_editor_criteria_form()
    {
    	$this->addElement('text', Criteria :: PROPERTY_TITLE, Translation :: get('Title'));
		$this->addRule(Criteria :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->add_html_editor(Criteria :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
		$this->addRule(Criteria :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');
    	
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    
    // Create and Update functions (Competency)
    
	function create_competency()
    {
    	$cba_type = $this->cba_type;
    	$values = $this->exportValues();
    	
    	$cba_type->set_title($values[Competency :: PROPERTY_TITLE]);
    	$cba_type->set_description($values[Competency :: PROPERTY_DESCRIPTION]);
    	

   		return $cba_type->create();
    }
    
	function update_competency()
    {
    	$cba_type = $this->cba_type;
    	$values = $this->exportValues();

    	$cba_type->set_title($values[Competency :: PROPERTY_TITLE]);
    	$cba_type->set_description($values[Competency :: PROPERTY_DESCRIPTION]);

    	return $cba_type->update();
    }
    

    // Create and Update functions (Indicator)
    
	function create_indicator()
    {
    	$cba_type = $this->cba_type;
    	$values = $this->exportValues();
    	
    	$cba_type->set_title($values[Indicator :: PROPERTY_TITLE]);
    	$cba_type->set_description($values[Indicator :: PROPERTY_DESCRIPTION]);

   		return $cba_type->create();
    }
    
	function update_indicator()
    {
    	$cba_type = $this->cba_type;
    	$values = $this->exportValues();

    	$cba_type->set_title($values[Indicator :: PROPERTY_TITLE]);
    	$cba_type->set_description($values[Indicator :: PROPERTY_DESCRIPTION]);

    	return $cba_type->update();
    }
    
    
    // Create and Update functions (Criteria)
    
	function create_criteria()
    {
    	$cba_type = $this->cba_type;
    	$values = $this->exportValues();
    	
    	$cba_type->set_title($values[Criteria :: PROPERTY_TITLE]);
    	$cba_type->set_description($values[Criteria :: PROPERTY_DESCRIPTION]);

   		return $cba_type->create();
    }
    
	function update_criteria()
    {
    	$cba_type = $this->cba_type;
    	$values = $this->exportValues();

    	$cba_type->set_title($values[Criteria :: PROPERTY_TITLE]);
    	$cba_type->set_description($values[Criteria :: PROPERTY_DESCRIPTION]);

    	return $cba_type->update();
    }

    
	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setCompetencyDefaults($defaults = array ())
	{
		$cba_type = $this->cba_type;

		$defaults[Competency :: PROPERTY_ID] = $cba_type->get_id();
    	$defaults[Competency :: PROPERTY_TITLE] = $cba_type->get_title();
    	$defaults[Competency :: PROPERTY_DESCRIPTION] = $cba_type->get_description();

		parent :: setDefaults($defaults);
	}
	
	function setIndicatorDefaults($defaults = array ())
	{
		$cba_type = $this->cba_type;

		$defaults[Indicator :: PROPERTY_ID] = $cba_type->get_id();
    	$defaults[Indicator :: PROPERTY_TITLE] = $cba_type->get_title();
    	$defaults[Indicator :: PROPERTY_DESCRIPTION] = $cba_type->get_description();

		parent :: setDefaults($defaults);
	}
	
	function setCriteriaDefaults($defaults = array ())
	{
		$cba_type = $this->cba_type;

		$defaults[Criteria :: PROPERTY_ID] = $cba_type->get_id();
    	$defaults[Criteria :: PROPERTY_TITLE] = $cba_type->get_title();
    	$defaults[Criteria :: PROPERTY_DESCRIPTION] = $cba_type->get_description();

		parent :: setDefaults($defaults);
	}
}
?>