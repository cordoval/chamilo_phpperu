<?php
require_once dirname(__FILE__) . '/../moment.class.php';

/**
 * This class describes the form for a Moment object.
 * @author Sven Vanpoucke
 * @author ehb
 **/
class MomentForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $moment;
	private $user;

    function MomentForm($form_type, $moment, $action, $user)
    {
    	parent :: __construct('moment_settings', 'post', $action);

    	$this->moment = $moment;
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
		$this->addElement('text', Moment :: PROPERTY_ID, Translation :: get('Id'));
		$this->addRule(Moment :: PROPERTY_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Moment :: PROPERTY_USER_ID, Translation :: get('UserId'));
		$this->addRule(Moment :: PROPERTY_USER_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Moment :: PROPERTY_BEGIN, Translation :: get('Begin'));
		$this->addRule(Moment :: PROPERTY_BEGIN, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Moment :: PROPERTY_END, Translation :: get('End'));
		$this->addRule(Moment :: PROPERTY_END, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Moment :: PROPERTY_CATEGORY_ID, Translation :: get('CategoryId'));
		$this->addRule(Moment :: PROPERTY_CATEGORY_ID, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', Moment :: PROPERTY_ID);

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

    function update_moment()
    {
    	$moment = $this->moment;
    	$values = $this->exportValues();

    	$moment->set_id($values[Moment :: PROPERTY_ID]);
    	$moment->set_user_id($values[Moment :: PROPERTY_USER_ID]);
    	$moment->set_begin($values[Moment :: PROPERTY_BEGIN]);
    	$moment->set_end($values[Moment :: PROPERTY_END]);
    	$moment->set_category_id($values[Moment :: PROPERTY_CATEGORY_ID]);

    	return $moment->update();
    }

    function create_moment()
    {
    	$moment = $this->moment;
    	$values = $this->exportValues();

    	$moment->set_id($values[Moment :: PROPERTY_ID]);
    	$moment->set_user_id($values[Moment :: PROPERTY_USER_ID]);
    	$moment->set_begin($values[Moment :: PROPERTY_BEGIN]);
    	$moment->set_end($values[Moment :: PROPERTY_END]);
    	$moment->set_category_id($values[Moment :: PROPERTY_CATEGORY_ID]);

   		return $moment->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$moment = $this->moment;

    	$defaults[Moment :: PROPERTY_ID] = $moment->get_id();
    	$defaults[Moment :: PROPERTY_USER_ID] = $moment->get_user_id();
    	$defaults[Moment :: PROPERTY_BEGIN] = $moment->get_begin();
    	$defaults[Moment :: PROPERTY_END] = $moment->get_end();
    	$defaults[Moment :: PROPERTY_CATEGORY_ID] = $moment->get_category_id();

		parent :: setDefaults($defaults);
	}
}
?>