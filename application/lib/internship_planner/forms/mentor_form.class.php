<?php
require_once dirname(__FILE__) . '/../mentor.class.php';

/**
 * This class describes the form for a Mentor object.
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 **/
class MentorForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $mentor;
	private $user;

    function MentorForm($form_type, $mentor, $action, $user)
    {
    	parent :: __construct('mentor_settings', 'post', $action);

    	$this->mentor = $mentor;
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
		$this->addElement('text', Mentor :: PROPERTY_ID, Translation :: get('Id'));
		$this->addRule(Mentor :: PROPERTY_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Mentor :: PROPERTY_TITLE, Translation :: get('Title'));
		$this->addRule(Mentor :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Mentor :: PROPERTY_FIRSTNAME, Translation :: get('Firstname'));
		$this->addRule(Mentor :: PROPERTY_FIRSTNAME, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Mentor :: PROPERTY_LASTNAME, Translation :: get('Lastname'));
		$this->addRule(Mentor :: PROPERTY_LASTNAME, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Mentor :: PROPERTY_EMAIL, Translation :: get('Email'));
		$this->addRule(Mentor :: PROPERTY_EMAIL, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Mentor :: PROPERTY_TELEPHONE, Translation :: get('Telephone'));
		$this->addRule(Mentor :: PROPERTY_TELEPHONE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Mentor :: PROPERTY_USER_ID, Translation :: get('UserId'));
		$this->addRule(Mentor :: PROPERTY_USER_ID, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', Mentor :: PROPERTY_ID);

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

    function update_mentor()
    {
    	$mentor = $this->mentor;
    	$values = $this->exportValues();

    	$mentor->set_id($values[Mentor :: PROPERTY_ID]);
    	$mentor->set_title($values[Mentor :: PROPERTY_TITLE]);
    	$mentor->set_firstname($values[Mentor :: PROPERTY_FIRSTNAME]);
    	$mentor->set_lastname($values[Mentor :: PROPERTY_LASTNAME]);
    	$mentor->set_email($values[Mentor :: PROPERTY_EMAIL]);
    	$mentor->set_telephone($values[Mentor :: PROPERTY_TELEPHONE]);
    	$mentor->set_user_id($values[Mentor :: PROPERTY_USER_ID]);

    	return $mentor->update();
    }

    function create_mentor()
    {
    	$mentor = $this->mentor;
    	$values = $this->exportValues();

    	$mentor->set_id($values[Mentor :: PROPERTY_ID]);
    	$mentor->set_title($values[Mentor :: PROPERTY_TITLE]);
    	$mentor->set_firstname($values[Mentor :: PROPERTY_FIRSTNAME]);
    	$mentor->set_lastname($values[Mentor :: PROPERTY_LASTNAME]);
    	$mentor->set_email($values[Mentor :: PROPERTY_EMAIL]);
    	$mentor->set_telephone($values[Mentor :: PROPERTY_TELEPHONE]);
    	$mentor->set_user_id($values[Mentor :: PROPERTY_USER_ID]);

   		return $mentor->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$mentor = $this->mentor;

    	$defaults[Mentor :: PROPERTY_ID] = $mentor->get_id();
    	$defaults[Mentor :: PROPERTY_TITLE] = $mentor->get_title();
    	$defaults[Mentor :: PROPERTY_FIRSTNAME] = $mentor->get_firstname();
    	$defaults[Mentor :: PROPERTY_LASTNAME] = $mentor->get_lastname();
    	$defaults[Mentor :: PROPERTY_EMAIL] = $mentor->get_email();
    	$defaults[Mentor :: PROPERTY_TELEPHONE] = $mentor->get_telephone();
    	$defaults[Mentor :: PROPERTY_USER_ID] = $mentor->get_user_id();

		parent :: setDefaults($defaults);
	}
}
?>