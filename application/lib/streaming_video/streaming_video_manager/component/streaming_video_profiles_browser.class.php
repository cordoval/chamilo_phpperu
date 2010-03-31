<?php
/**
 * @package application.streaming_video.streaming_video.component
 */

require_once dirname(__FILE__).'/../streaming_video_manager.class.php';
require_once dirname(__FILE__).'/../streaming_video_manager_component.class.php';

/**
 * streaming_video component which allows the user to browse his profiles
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
class StreamingVideoManagerProfilesBrowserComponent extends StreamingVideoManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE)), Translation :: get('BrowseStreamingVideo')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseProfiles')));

		$this->display_header($trail);

		echo '<a href="' . $this->get_create_profile_url() . '">' . Translation :: get('CreateProfile') . '</a>';
		echo '<br /><br />';

		$profiles = $this->retrieve_profiles();
		while($profile = $profiles->next_result())
		{
			echo '<div style="border: 1px solid grey; padding: 5px;">';
			dump($profile);
			echo '<br /><a href="' . $this->get_update_profile_url($profile). '">' . Translation :: get('UpdateProfile') . '</a>';
			echo ' | <a href="' . $this->get_delete_profile_url($profile) . '">' . Translation :: get('DeleteProfile') . '</a>';
			echo '</div><br /><br />';
		}

		$this->display_footer();
	}

}
?>