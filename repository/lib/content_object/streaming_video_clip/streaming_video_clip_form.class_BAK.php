<?php

/**
 * Description of streaming_video_clip_formclass
 *
 * @author jevdheyd
 */

require_once Path :: get_application_path().'/lib/streaming_video/upload_account.class.php';

class StreamingVideoClipForm extends ContentObjectForm {

    const PARAM_CUE_POINTS = 'cuepoints';

	protected function build_creation_form()
	{
		// No call to parent, as we don't want the default form elements
                //
                //create uploadaccount for user or get existing one
                $account = UploadAccount :: get();
		$username = $account->get_username();
                $password = $account->get_upload_password();

                $url = Path::get('WEB_APP_PATH')
			. 'lib/streaming_video/jnlp.php?username=' . htmlspecialchars($username)
			. '&password=' . htmlspecialchars($password);
		// TODO: add usage instructions
		$this->addElement('static', 'instructions',
			'',
			'<p>'
			. htmlspecialchars(Translation::get('ClipUploaderInstructions'))
			. '</p>');
		$this->addElement('link', 'uploader_link',
			Translation :: get('ClipUploader'),
			$url,
			Translation :: get('LaunchClipUploader'));

	}

	function create_content_object()
	{
		// No call to parent, as creation happens upon upload completion
	}

	protected function build_editing_form()
	{
		parent :: build_editing_form();
		// TODO: friendly UI
		// TODO: compare times to clip length
		$this->addElement(
			'textarea',
			self::PARAM_CUE_POINTS,
			Translation::get('VideoClipCuePoints'),
			array('style' => 'width: 650px', 'rows' => 5));
	}

	function update_content_object() {

            $result = parent::update_content_object();
		if ($result != self::RESULT_SUCCESS) {
			return $result;
		}
		$object = $this->get_content_object();

		$cuepoints_text = $this->exportValue(self::PARAM_CUE_POINTS);
		$lines = split("[\n\r]+", trim($cuepoints_text));
		$cuepoints = array();
		foreach ($lines as $line) {
			list($time, $title) = split("[ =]+", trim($line), 2);
			if ($title) {
				$parts = explode(':', $time, 3);
				$seconds = floatval(array_pop($parts));
				$minutes = intval(array_pop($parts));
				$hours = intval(array_pop($parts));
				$seconds = $seconds + ($minutes + $hours * 60) * 60;
				if ($seconds > 0) {
					$cuepoints[round($seconds * 1000)] = $title;
				}
			}
		}
		// Update existing cue points and remove the ones that were not specified
		$cond = new EqualityCondition(StreamingVideoClipCuePoint :: PROPERTY_PARENT_ID,
			$object->get_id());
		$children = RepositoryDataManager::get_instance()
			->retrieve_content_objects('streaming_video_clip_cue_point', $cond);
		while ($child = $children->next_result()) {
			$t = $child->get_start_time();
			if (array_key_exists($t, $cuepoints)) {
				$child->set_title($cuepoints[$t]);
				$child->update();
				unset($cuepoints[$t]);
			}
			else {
				// XXX: Returns false if the cue point is used anywhere
				$child->delete();
			}
		}
		// Add new cue points
		foreach ($cuepoints as $time => $title) {
			$cp = new StreamingVideoClipCuePoint();
			$cp->set_owner_id($object->get_owner_id());
			$cp->set_title($title);
			$cp->set_description(htmlspecialchars($title));
			$cp->set_parent_id($object->get_id());
			$cp->set_start_time($time);
			$cp->create();
		}
		return self::RESULT_SUCCESS;
	}

	function setDefaults($defaults = array()) {
		$lo = $this->get_content_object();
		if (isset($lo)) {
			$cps = $lo->get_cue_points();
			if (!empty($cps)) {
				$str = '';
				foreach ($cps as $cp) {
					$str .= self::format_duration($cp->get_start_time())
						. ' = '
						. $cp->get_title()
						. "\r\n";
				}
				$defaults[self::PARAM_CUE_POINTS] = $str;
			}
		}
		parent::setDefaults($defaults);
	}

	private static function format_duration($milliseconds) {
		$res = array();
		foreach (array(1000 * 60 * 60, 1000 * 60, 1000) as $d) {
			$c = floor($milliseconds / $d);
			$milliseconds -= $c * $d;
			$res[] = $c;
		}
		return sprintf('%d:%02d:%02d.%03d',
			$res[0], $res[1], $res[2], $milliseconds);
	}

	function validate() {
		if ($this->get_form_type() == self :: TYPE_CREATE) {
			return false;
		}
		return parent :: validate();
	}

	protected function add_footer() {
		// Don't print OK button when creating
		if ($this->get_form_type() != self :: TYPE_CREATE) {
			parent :: add_footer();
		}
	}

}
?>