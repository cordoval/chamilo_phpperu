<?php
require_once dirname(__FILE__) . '/../streaming_video_transcoding.class.php';

/**
 * This class describes the form for a StreamingVideoTranscoding object.
 * @author Sven Vanpoucke
 * @author jevdheyd
 **/
class StreamingVideoTranscodingForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $streaming_video_transcoding;
	private $user;

    function StreamingVideoTranscodingForm($form_type, $streaming_video_transcoding, $action, $user)
    {
    	parent :: __construct('streaming_video_transcoding_settings', 'post', $action);

    	$this->streaming_video_transcoding = $streaming_video_transcoding;
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
		$this->addElement('text', StreamingVideoTranscoding :: PROPERTY_CLIP_ID, Translation :: get('ClipId'));
		$this->addRule(StreamingVideoTranscoding :: PROPERTY_CLIP_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', StreamingVideoTranscoding :: PROPERTY_SOURCE_FILE, Translation :: get('SourceFile'));
		$this->addRule(StreamingVideoTranscoding :: PROPERTY_SOURCE_FILE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', StreamingVideoTranscoding :: PROPERTY_START_TIME, Translation :: get('StartTime'));
		$this->addRule(StreamingVideoTranscoding :: PROPERTY_START_TIME, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', StreamingVideoTranscoding :: PROPERTY_END_TIME, Translation :: get('EndTime'));
		$this->addRule(StreamingVideoTranscoding :: PROPERTY_END_TIME, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', StreamingVideoTranscoding :: PROPERTY_ID);

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

    function update_streaming_video_transcoding()
    {
    	$streaming_video_transcoding = $this->streaming_video_transcoding;
    	$values = $this->exportValues();

    	$streaming_video_transcoding->set_clip_id($values[StreamingVideoTranscoding :: PROPERTY_CLIP_ID]);
    	$streaming_video_transcoding->set_source_file($values[StreamingVideoTranscoding :: PROPERTY_SOURCE_FILE]);
    	$streaming_video_transcoding->set_start_time($values[StreamingVideoTranscoding :: PROPERTY_START_TIME]);
    	$streaming_video_transcoding->set_end_time($values[StreamingVideoTranscoding :: PROPERTY_END_TIME]);

    	return $streaming_video_transcoding->update();
    }

    function create_streaming_video_transcoding()
    {
    	$streaming_video_transcoding = $this->streaming_video_transcoding;
    	$values = $this->exportValues();

    	$streaming_video_transcoding->set_clip_id($values[StreamingVideoTranscoding :: PROPERTY_CLIP_ID]);
    	$streaming_video_transcoding->set_source_file($values[StreamingVideoTranscoding :: PROPERTY_SOURCE_FILE]);
    	$streaming_video_transcoding->set_start_time($values[StreamingVideoTranscoding :: PROPERTY_START_TIME]);
    	$streaming_video_transcoding->set_end_time($values[StreamingVideoTranscoding :: PROPERTY_END_TIME]);

   		return $streaming_video_transcoding->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$streaming_video_transcoding = $this->streaming_video_transcoding;

    	$defaults[StreamingVideoTranscoding :: PROPERTY_CLIP_ID] = $streaming_video_transcoding->get_clip_id();
    	$defaults[StreamingVideoTranscoding :: PROPERTY_SOURCE_FILE] = $streaming_video_transcoding->get_source_file();
    	$defaults[StreamingVideoTranscoding :: PROPERTY_START_TIME] = $streaming_video_transcoding->get_start_time();
    	$defaults[StreamingVideoTranscoding :: PROPERTY_END_TIME] = $streaming_video_transcoding->get_end_time();

		parent :: setDefaults($defaults);
	}
}
?>