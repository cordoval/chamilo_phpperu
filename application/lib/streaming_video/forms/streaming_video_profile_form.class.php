<?php
require_once dirname(__FILE__) . '/../profile.class.php';

/**
 * This class describes the form for a Profile object.
 * @author Sven Vanpoucke
 * @author jevdheyd
 **/
class ProfileForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $profile;
	private $user;

    function ProfileForm($form_type, $profile, $action, $user)
    {
    	parent :: __construct('profile_settings', 'post', $action);

    	$this->profile = $profile;
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
		$this->addElement('text', Profile :: PROPERTY_POSITION, Translation :: get('Position'));
		$this->addRule(Profile :: PROPERTY_POSITION, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Profile :: PROPERTY_NAME, Translation :: get('Name'));
		$this->addRule(Profile :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Profile :: PROPERTY_AUDIO_QUALITY, Translation :: get('AudioQuality'));
		$this->addRule(Profile :: PROPERTY_AUDIO_QUALITY, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Profile :: PROPERTY_VIDEO_QUALITY, Translation :: get('VideoQuality'));
		$this->addRule(Profile :: PROPERTY_VIDEO_QUALITY, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Profile :: PROPERTY_CHANNELS, Translation :: get('Channels'));
		$this->addRule(Profile :: PROPERTY_CHANNELS, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Profile :: PROPERTY_WIDTH, Translation :: get('Width'));
		$this->addRule(Profile :: PROPERTY_WIDTH, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Profile :: PROPERTY_HEIGHT, Translation :: get('Height'));
		$this->addRule(Profile :: PROPERTY_HEIGHT, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Profile :: PROPERTY_END_TIME, Translation :: get('EndTime'));
		$this->addRule(Profile :: PROPERTY_END_TIME, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', Profile :: PROPERTY_ID);

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

    function update_profile()
    {
    	$profile = $this->profile;
    	$values = $this->exportValues();

    	$profile->set_position($values[Profile :: PROPERTY_POSITION]);
    	$profile->set_name($values[Profile :: PROPERTY_NAME]);
    	$profile->set_audio_quality($values[Profile :: PROPERTY_AUDIO_QUALITY]);
    	$profile->set_video_quality($values[Profile :: PROPERTY_VIDEO_QUALITY]);
    	$profile->set_channels($values[Profile :: PROPERTY_CHANNELS]);
    	$profile->set_width($values[Profile :: PROPERTY_WIDTH]);
    	$profile->set_height($values[Profile :: PROPERTY_HEIGHT]);
    	$profile->set_end_time($values[Profile :: PROPERTY_END_TIME]);

    	return $profile->update();
    }

    function create_profile()
    {
    	$profile = $this->profile;
    	$values = $this->exportValues();

    	$profile->set_position($values[Profile :: PROPERTY_POSITION]);
    	$profile->set_name($values[Profile :: PROPERTY_NAME]);
    	$profile->set_audio_quality($values[Profile :: PROPERTY_AUDIO_QUALITY]);
    	$profile->set_video_quality($values[Profile :: PROPERTY_VIDEO_QUALITY]);
    	$profile->set_channels($values[Profile :: PROPERTY_CHANNELS]);
    	$profile->set_width($values[Profile :: PROPERTY_WIDTH]);
    	$profile->set_height($values[Profile :: PROPERTY_HEIGHT]);
    	$profile->set_end_time($values[Profile :: PROPERTY_END_TIME]);

   		return $profile->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$profile = $this->profile;

    	$defaults[Profile :: PROPERTY_POSITION] = $profile->get_position();
    	$defaults[Profile :: PROPERTY_NAME] = $profile->get_name();
    	$defaults[Profile :: PROPERTY_AUDIO_QUALITY] = $profile->get_audio_quality();
    	$defaults[Profile :: PROPERTY_VIDEO_QUALITY] = $profile->get_video_quality();
    	$defaults[Profile :: PROPERTY_CHANNELS] = $profile->get_channels();
    	$defaults[Profile :: PROPERTY_WIDTH] = $profile->get_width();
    	$defaults[Profile :: PROPERTY_HEIGHT] = $profile->get_height();
    	$defaults[Profile :: PROPERTY_END_TIME] = $profile->get_end_time();

		parent :: setDefaults($defaults);
	}
}
?>