<?php
require_once dirname(__FILE__) . '/../period.class.php';

/**
 * This class describes the form for a Period object.
 * @author Sven Vanpoucke
 * @author ehb
 **/
class PeriodForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $period;
	private $user;

    function PeriodForm($form_type, $period, $action, $user)
    {
    	parent :: __construct('period_settings', 'post', $action);

    	$this->period = $period;
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
		$this->addElement('text', Period :: PROPERTY_ID, Translation :: get('Id'));
		$this->addRule(Period :: PROPERTY_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Period :: PROPERTY_NAME, Translation :: get('Name'));
		$this->addRule(Period :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Period :: PROPERTY_BEGIN, Translation :: get('Begin'));
		$this->addRule(Period :: PROPERTY_BEGIN, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Period :: PROPERTY_END, Translation :: get('End'));
		$this->addRule(Period :: PROPERTY_END, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', Period :: PROPERTY_ID);

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

    function update_period()
    {
    	$period = $this->period;
    	$values = $this->exportValues();

    	$period->set_id($values[Period :: PROPERTY_ID]);
    	$period->set_name($values[Period :: PROPERTY_NAME]);
    	$period->set_begin($values[Period :: PROPERTY_BEGIN]);
    	$period->set_end($values[Period :: PROPERTY_END]);

    	return $period->update();
    }

    function create_period()
    {
    	$period = $this->period;
    	$values = $this->exportValues();

    	$period->set_id($values[Period :: PROPERTY_ID]);
    	$period->set_name($values[Period :: PROPERTY_NAME]);
    	$period->set_begin($values[Period :: PROPERTY_BEGIN]);
    	$period->set_end($values[Period :: PROPERTY_END]);

   		return $period->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$period = $this->period;

    	$defaults[Period :: PROPERTY_ID] = $period->get_id();
    	$defaults[Period :: PROPERTY_NAME] = $period->get_name();
    	$defaults[Period :: PROPERTY_BEGIN] = $period->get_begin();
    	$defaults[Period :: PROPERTY_END] = $period->get_end();

		parent :: setDefaults($defaults);
	}
}
?>