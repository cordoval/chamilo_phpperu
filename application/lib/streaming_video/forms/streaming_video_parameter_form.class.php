<?php
require_once dirname(__FILE__) . '/../parameter.class.php';

/**
 * This class describes the form for a Parameter object.
 * @author Sven Vanpoucke
 * @author jevdheyd
 **/
class ParameterForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $parameter;
	private $user;

    function ParameterForm($form_type, $parameter, $action, $user)
    {
    	parent :: __construct('parameter_settings', 'post', $action);

    	$this->parameter = $parameter;
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
		$this->addElement('text', Parameter :: PROPERTY_NAME, Translation :: get('Name'));
		$this->addRule(Parameter :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Parameter :: PROPERTY_VALUE, Translation :: get('Value'));
		$this->addRule(Parameter :: PROPERTY_VALUE, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', Parameter :: PROPERTY_ID);

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

    function update_parameter()
    {
    	$parameter = $this->parameter;
    	$values = $this->exportValues();

    	$parameter->set_name($values[Parameter :: PROPERTY_NAME]);
    	$parameter->set_value($values[Parameter :: PROPERTY_VALUE]);

    	return $parameter->update();
    }

    function create_parameter()
    {
    	$parameter = $this->parameter;
    	$values = $this->exportValues();

    	$parameter->set_name($values[Parameter :: PROPERTY_NAME]);
    	$parameter->set_value($values[Parameter :: PROPERTY_VALUE]);

   		return $parameter->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$parameter = $this->parameter;

    	$defaults[Parameter :: PROPERTY_NAME] = $parameter->get_name();
    	$defaults[Parameter :: PROPERTY_VALUE] = $parameter->get_value();

		parent :: setDefaults($defaults);
	}
}
?>