<?php
/**
 * @package application.streaming_video.streaming_video.component
 */
require_once dirname(__FILE__).'/../streaming_video_manager.class.php';
require_once dirname(__FILE__).'/../streaming_video_manager_component.class.php';

/**
 * Component to delete streaming_video_transcodings objects
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
class StreamingVideoManagerStreamingVideoTranscodingDeleterComponent extends StreamingVideoManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[StreamingVideoManager :: PARAM_STREAMING_VIDEO_TRANSCODING];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$streaming_video_transcoding = $this->retrieve_streaming_video_transcoding($id);

				if (!$streaming_video_transcoding->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedStreamingVideoTranscodingNotDeleted';
				}
				else
				{
					$message = 'Selected{StreamingVideoTranscodingsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedStreamingVideoTranscodingDeleted';
				}
				else
				{
					$message = 'SelectedStreamingVideoTranscodingsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE_STREAMING_VIDEO_TRANSCODINGS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoStreamingVideoTranscodingsSelected')));
		}
	}
}
?>