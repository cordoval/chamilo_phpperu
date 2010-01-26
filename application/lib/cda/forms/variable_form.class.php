<?php
require_once dirname(__FILE__) . '/../variable.class.php';

/**
 * This class describes the form for a Variable object.
 * @author Sven Vanpoucke
 * @author 
 **/
class VariableForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $variable;
	private $user;

    function VariableForm($form_type, $variable, $action, $user)
    {
    	parent :: __construct('variable_settings', 'post', $action);

    	$this->variable = $variable;
    	$this->user = $user;
		$this->form_type = $form_type;

		if ($this->form_type == self :: TYPE_EDIT)
		{
			$this->build_editing_form();
		}
		elseif ($this->form_type == self :: TYPE_CREATE)
		{
			$this->build_creation_form();
		}

		$this->setDefaults();
    }

    function build_basic_form()
    {
		$this->addElement('text', Variable :: PROPERTY_ID, Translation :: get('Id'));
		$this->addRule(Variable :: PROPERTY_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Variable :: PROPERTY_VARIABLE, Translation :: get('Variable'));
		$this->addRule(Variable :: PROPERTY_VARIABLE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Variable :: PROPERTY_LANGUAGE_PACK_ID, Translation :: get('LanguagePackId'));
		$this->addRule(Variable :: PROPERTY_LANGUAGE_PACK_ID, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', Variable :: PROPERTY_ID);

		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
    	$this->build_basic_form();

		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_variable()
    {
    	$variable = $this->variable;
    	$values = $this->exportValues();

    	$variable->set_id($values[Variable :: PROPERTY_ID]);
    	$variable->set_variable($values[Variable :: PROPERTY_VARIABLE]);
    	$variable->set_language_pack_id($values[Variable :: PROPERTY_LANGUAGE_PACK_ID]);

    	return $variable->update();
    }

    function create_variable()
    {
    	$variable = $this->variable;
    	$values = $this->exportValues();

    	$variable->set_id($values[Variable :: PROPERTY_ID]);
    	$variable->set_variable($values[Variable :: PROPERTY_VARIABLE]);
    	$variable->set_language_pack_id($values[Variable :: PROPERTY_LANGUAGE_PACK_ID]);

   		return $variable->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$variable = $this->variable;

    	$defaults[Variable :: PROPERTY_ID] = $variable->get_id();
    	$defaults[Variable :: PROPERTY_VARIABLE] = $variable->get_variable();
    	$defaults[Variable :: PROPERTY_LANGUAGE_PACK_ID] = $variable->get_language_pack_id();

		parent :: setDefaults($defaults);
	}
}
?>