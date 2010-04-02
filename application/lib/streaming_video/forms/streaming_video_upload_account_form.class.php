<?php
require_once dirname(__FILE__) . '/../upload_account.class.php';

/**
 * This class describes the form for a UploadAccount object.
 * @author Sven Vanpoucke
 * @author jevdheyd
 **/
class UploadAccountForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $upload_account;
	private $user;

    function UploadAccountForm($form_type, $upload_account, $action, $user)
    {
    	parent :: __construct('upload_account_settings', 'post', $action);

    	$this->upload_account = $upload_account;
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
		$this->addElement('text', UploadAccount :: PROPERTY_ID, Translation :: get('Id'));
		$this->addRule(UploadAccount :: PROPERTY_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', UploadAccount :: PROPERTY_USER_ID, Translation :: get('UserId'));
		$this->addRule(UploadAccount :: PROPERTY_USER_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', UploadAccount :: PROPERTY_EXPIRES, Translation :: get('Expires'));
		$this->addRule(UploadAccount :: PROPERTY_EXPIRES, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', UploadAccount :: PROPERTY_UPLOAD_PASSWORD, Translation :: get('UploadPassword'));
		$this->addRule(UploadAccount :: PROPERTY_UPLOAD_PASSWORD, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', UploadAccount :: PROPERTY_ID);

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

    function update_upload_account()
    {
    	$upload_account = $this->upload_account;
    	$values = $this->exportValues();

    	$upload_account->set_id($values[UploadAccount :: PROPERTY_ID]);
    	$upload_account->set_user_id($values[UploadAccount :: PROPERTY_USER_ID]);
    	$upload_account->set_expires($values[UploadAccount :: PROPERTY_EXPIRES]);
    	$upload_account->set_upload_password($values[UploadAccount :: PROPERTY_UPLOAD_PASSWORD]);

    	return $upload_account->update();
    }

    function create_upload_account()
    {
    	$upload_account = $this->upload_account;
    	$values = $this->exportValues();

    	$upload_account->set_id($values[UploadAccount :: PROPERTY_ID]);
    	$upload_account->set_user_id($values[UploadAccount :: PROPERTY_USER_ID]);
    	$upload_account->set_expires($values[UploadAccount :: PROPERTY_EXPIRES]);
    	$upload_account->set_upload_password($values[UploadAccount :: PROPERTY_UPLOAD_PASSWORD]);

   		return $upload_account->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$upload_account = $this->upload_account;

    	$defaults[UploadAccount :: PROPERTY_ID] = $upload_account->get_id();
    	$defaults[UploadAccount :: PROPERTY_USER_ID] = $upload_account->get_user_id();
    	$defaults[UploadAccount :: PROPERTY_EXPIRES] = $upload_account->get_expires();
    	$defaults[UploadAccount :: PROPERTY_UPLOAD_PASSWORD] = $upload_account->get_upload_password();

		parent :: setDefaults($defaults);
	}
}
?>