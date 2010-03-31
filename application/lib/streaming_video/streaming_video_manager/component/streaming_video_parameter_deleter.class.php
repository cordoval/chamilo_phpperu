<?php
/**
 * @package application.streaming_video.streaming_video.component
 */
require_once dirname(__FILE__).'/../streaming_video_manager.class.php';
require_once dirname(__FILE__).'/../streaming_video_manager_component.class.php';

/**
 * Component to delete parameters objects
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
class StreamingVideoManagerParameterDeleterComponent extends StreamingVideoManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[StreamingVideoManager :: PARAM_parameter];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$parameter = $this->retrieve_parameter($id);

				if (!$parameter->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedParameterNotDeleted';
				}
				else
				{
					$message = 'Selected{ParametersNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedParameterDeleted';
				}
				else
				{
					$message = 'SelectedParametersDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE_parameterS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoParametersSelected')));
		}
	}
}
?>