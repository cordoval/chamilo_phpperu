<?php
require_once dirname(__FILE__) . '/../streaming_video_ftp_account.class.php';

/**
 * This class describes the form for a StreamingVideoFtpAccount object.
 * @author Sven Vanpoucke
 * @author jevdheyd
 **/
class StreamingVideoFtpAccountForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $streaming_video_ftp_account;
	private $user;

    function StreamingVideoFtpAccountForm($form_type, $streaming_video_ftp_account, $action, $user)
    {
    	parent :: __construct('streaming_video_ftp_account_settings', 'post', $action);

    	$this->streaming_video_ftp_account = $streaming_video_ftp_account;
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
    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', StreamingVideoFtpAccount :: PROPERTY_ID);

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

    function update_streaming_video_ftp_account()
    {
    	$streaming_video_ftp_account = $this->streaming_video_ftp_account;
    	$values = $this->exportValues();


    	return $streaming_video_ftp_account->update();
    }

    function create_streaming_video_ftp_account()
    {
    	$streaming_video_ftp_account = $this->streaming_video_ftp_account;
    	$values = $this->exportValues();


   		return $streaming_video_ftp_account->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$streaming_video_ftp_account = $this->streaming_video_ftp_account;


		parent :: setDefaults($defaults);
	}
}
?>