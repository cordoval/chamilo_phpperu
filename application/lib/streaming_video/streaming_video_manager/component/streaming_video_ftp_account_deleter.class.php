<?php
/**
 * @package application.streaming_video.streaming_video.component
 */
require_once dirname(__FILE__).'/../streaming_video_manager.class.php';
require_once dirname(__FILE__).'/../streaming_video_manager_component.class.php';

/**
 * Component to delete streaming_video_ftp_accounts objects
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
class StreamingVideoManagerStreamingVideoFtpAccountDeleterComponent extends StreamingVideoManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[StreamingVideoManager :: PARAM_STREAMING_VIDEO_FTP_ACCOUNT];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$streaming_video_ftp_account = $this->retrieve_streaming_video_ftp_account($id);

				if (!$streaming_video_ftp_account->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedStreamingVideoFtpAccountNotDeleted';
				}
				else
				{
					$message = 'Selected{StreamingVideoFtpAccountsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedStreamingVideoFtpAccountDeleted';
				}
				else
				{
					$message = 'SelectedStreamingVideoFtpAccountsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE_STREAMING_VIDEO_FTP_ACCOUNTS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoStreamingVideoFtpAccountsSelected')));
		}
	}
}
?>