<?php
require_once dirname(__FILE__) . '/../indicator.class.php';
/**
 * This class describes a IndicatorForm object
 * 
 * @author Nick Van Loocke
 **/
class IndicatorForm extends FormValidator
{
	const TYPE_CREATOR_INDICATOR = 1;
	const TYPE_EDITOR_INDICATOR = 2;
	
	private $indicator;
	private $user;

    function IndicatorForm($form_type, $indicator, $action, $user)
    {
    	parent :: __construct('cba_settings', 'post', $action);

    	$this->indicator = $indicator;
    	$this->user = $user;
		$this->form_type = $form_type;

		if ($this->form_type == self :: TYPE_CREATOR_INDICATOR)
		{
			$this->build_creator_indicator_form();
			$this->setIndicatorDefaults();
		}
   	 	elseif ($this->form_type == self :: TYPE_EDITOR_INDICATOR)
		{
			$this->build_editor_indicator_form();
			$this->setIndicatorDefaults();
		}

    }
    
    // Forms
    
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


    // Create and Update functions (Indicator)
    
	function create_indicator()
    {
    	$indicator = $this->indicator;
    	$values = $this->exportValues();
    	
    	$indicator->set_title($values[Indicator :: PROPERTY_TITLE]);
    	$indicator->set_description($values[Indicator :: PROPERTY_DESCRIPTION]);

   		return $indicator->create();
    }
    
	function update_indicator()
    {
    	$indicator = $this->indicator;
    	$values = $this->exportValues();

    	$indicator->set_title($values[Indicator :: PROPERTY_TITLE]);
    	$indicator->set_description($values[Indicator :: PROPERTY_DESCRIPTION]);

    	return $indicator->update();
    }

    
	// Default values (setter)
	
	function setIndicatorDefaults($defaults = array ())
	{
		$indicator = $this->indicator;

		$defaults[Indicator :: PROPERTY_ID] = $indicator->get_id();
    	$defaults[Indicator :: PROPERTY_TITLE] = $indicator->get_title();
    	$defaults[Indicator :: PROPERTY_DESCRIPTION] = $indicator->get_description();

		parent :: setDefaults($defaults);
	}
}
?>