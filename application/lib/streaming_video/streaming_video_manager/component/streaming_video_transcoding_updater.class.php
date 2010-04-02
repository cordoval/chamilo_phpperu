<?php
/**
 * @package application.streaming_video.streaming_video.component
 */
require_once dirname(__FILE__).'/../streaming_video_manager.class.php';
require_once dirname(__FILE__).'/../streaming_video_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/streaming_video_transcoding_form.class.php';

/**
 * Component to edit an existing streaming_video_transcoding object
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
class StreamingVideoManagerStreamingVideoTranscodingUpdaterComponent extends StreamingVideoManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE)), Translation :: get('BrowseStreamingVideo')));
		$trail->add(new Breadcrumb($this->get_url(array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE_STREAMING_VIDEO_TRANSCODINGS)), Translation :: get('BrowseStreamingVideoTranscodings')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateStreamingVideoTranscoding')));

		$streaming_video_transcoding = $this->retrieve_streaming_video_transcoding(Request :: get(StreamingVideoManager :: PARAM_STREAMING_VIDEO_TRANSCODING));
		$form = new StreamingVideoTranscodingForm(StreamingVideoTranscodingForm :: TYPE_EDIT, $streaming_video_transcoding, $this->get_url(array(StreamingVideoManager :: PARAM_STREAMING_VIDEO_TRANSCODING => $streaming_video_transcoding->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_streaming_video_transcoding();
			$this->redirect($success ? Translation :: get('StreamingVideoTranscodingUpdated') : Translation :: get('StreamingVideoTranscodingNotUpdated'), !$success, array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE_STREAMING_VIDEO_TRANSCODINGS));
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