<?php
/**
 * @package application.streaming_video.streaming_video.component
 */

require_once dirname(__FILE__).'/../streaming_video_manager.class.php';
require_once dirname(__FILE__).'/../streaming_video_manager_component.class.php';

/**
 * streaming_video component which allows the user to browse his parameters
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
class StreamingVideoManagerParametersBrowserComponent extends StreamingVideoManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE)), Translation :: get('BrowseStreamingVideo')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseParameters')));

		$this->display_header($trail);

		echo '<a href="' . $this->get_create_parameter_url() . '">' . Translation :: get('CreateParameter') . '</a>';
		echo '<br /><br />';

		$parameters = $this->retrieve_parameters();
		while($parameter = $parameters->next_result())
		{
			echo '<div style="border: 1px solid grey; padding: 5px;">';
			dump($parameter);
			echo '<br /><a href="' . $this->get_update_parameter_url($parameter). '">' . Translation :: get('UpdateParameter') . '</a>';
			echo ' | <a href="' . $this->get_delete_parameter_url($parameter) . '">' . Translation :: get('DeleteParameter') . '</a>';
			echo '</div><br /><br />';
		}

		$this->display_footer();
	}

}
?>