<?php
/**
 * @package application.streaming_video.streaming_video.component
 */
require_once dirname(__FILE__).'/../streaming_video_manager.class.php';
require_once dirname(__FILE__).'/../streaming_video_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/profile_form.class.php';

/**
 * Component to edit an existing profile object
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
class StreamingVideoManagerProfileUpdaterComponent extends StreamingVideoManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE)), Translation :: get('BrowseStreamingVideo')));
		$trail->add(new Breadcrumb($this->get_url(array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE_profileS)), Translation :: get('BrowseProfiles')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateProfile')));

		$profile = $this->retrieve_profile(Request :: get(StreamingVideoManager :: PARAM_profile));
		$form = new ProfileForm(ProfileForm :: TYPE_EDIT, $profile, $this->get_url(array(StreamingVideoManager :: PARAM_profile => $profile->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_profile();
			$this->redirect($success ? Translation :: get('ProfileUpdated') : Translation :: get('ProfileNotUpdated'), !$success, array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE_profileS));
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