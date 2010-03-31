<?php
/**
 * @package application.streaming_video.streaming_video.component
 */
require_once dirname(__FILE__).'/../streaming_video_manager.class.php';
require_once dirname(__FILE__).'/../streaming_video_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/streaming_video_transcoding_form.class.php';

/**
 * Component to create a new streaming_video_transcoding object
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
class StreamingVideoManagerStreamingVideoTranscodingCreatorComponent extends StreamingVideoManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE)), Translation :: get('BrowseStreamingVideo')));
		$trail->add(new Breadcrumb($this->get_url(array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE_STREAMING_VIDEO_TRANSCODINGS)), Translation :: get('BrowseStreamingVideoTranscodings')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateStreamingVideoTranscoding')));

		$streaming_video_transcoding = new StreamingVideoTranscoding();
		$form = new StreamingVideoTranscodingForm(StreamingVideoTranscodingForm :: TYPE_CREATE, $streaming_video_transcoding, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_streaming_video_transcoding();
			$this->redirect($success ? Translation :: get('StreamingVideoTranscodingCreated') : Translation :: get('StreamingVideoTranscodingNotCreated'), !$success, array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE_STREAMING_VIDEO_TRANSCODINGS));
		}
		else
		{
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}
	}
}
?>