<?php
/**
 * @package application.streaming_video.streaming_video.component
 */
require_once dirname(__FILE__).'/../streaming_video_manager.class.php';
require_once dirname(__FILE__).'/../streaming_video_manager_component.class.php';

/**
 * Component to delete profiles objects
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
class StreamingVideoManagerProfileDeleterComponent extends StreamingVideoManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[StreamingVideoManager :: PARAM_profile];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$profile = $this->retrieve_profile($id);

				if (!$profile->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedProfileNotDeleted';
				}
				else
				{
					$message = 'Selected{ProfilesNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedProfileDeleted';
				}
				else
				{
					$message = 'SelectedProfilesDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE_profileS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoProfilesSelected')));
		}
	}
}
?>