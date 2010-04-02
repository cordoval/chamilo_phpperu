<?php
/**
 * @package application.streaming_video.streaming_video.component
 */
require_once dirname(__FILE__).'/../streaming_video_manager.class.php';
require_once dirname(__FILE__).'/../streaming_video_manager_component.class.php';

/**
 * Component to delete upload_accounts objects
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
class StreamingVideoManagerUploadAccountDeleterComponent extends StreamingVideoManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[StreamingVideoManager :: PARAM_upload_account];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$upload_account = $this->retrieve_upload_account($id);

				if (!$upload_account->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedUploadAccountNotDeleted';
				}
				else
				{
					$message = 'Selected{UploadAccountsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedUploadAccountDeleted';
				}
				else
				{
					$message = 'SelectedUploadAccountsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE_upload_accountS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoUploadAccountsSelected')));
		}
	}
}
?>