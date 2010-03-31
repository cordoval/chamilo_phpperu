<?php
/**
 * @package application.streaming_video.streaming_video.component
 */

require_once dirname(__FILE__).'/../streaming_video_manager.class.php';
require_once dirname(__FILE__).'/../streaming_video_manager_component.class.php';

/**
 * streaming_video component which allows the user to browse his streaming_video_transcodings
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
class StreamingVideoManagerStreamingVideoTranscodingsBrowserComponent extends StreamingVideoManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE)), Translation :: get('BrowseStreamingVideo')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseStreamingVideoTranscodings')));

		$this->display_header($trail);

		echo '<a href="' . $this->get_create_streaming_video_transcoding_url() . '">' . Translation :: get('CreateStreamingVideoTranscoding') . '</a>';
		echo '<br /><br />';

		$streaming_video_transcodings = $this->retrieve_streaming_video_transcodings();
		while($streaming_video_transcoding = $streaming_video_transcodings->next_result())
		{
			echo '<div style="border: 1px solid grey; padding: 5px;">';
			dump($streaming_video_transcoding);
			echo '<br /><a href="' . $this->get_update_streaming_video_transcoding_url($streaming_video_transcoding). '">' . Translation :: get('UpdateStreamingVideoTranscoding') . '</a>';
			echo ' | <a href="' . $this->get_delete_streaming_video_transcoding_url($streaming_video_transcoding) . '">' . Translation :: get('DeleteStreamingVideoTranscoding') . '</a>';
			echo '</div><br /><br />';
		}

		$this->display_footer();
	}

}
?>