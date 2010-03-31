<?php
/**
 * @package application.streaming_video.streaming_video.component
 */
require_once dirname(__FILE__).'/../streaming_video_manager.class.php';
require_once dirname(__FILE__).'/../streaming_video_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/profile_form.class.php';

/**
 * Component to create a new profile object
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
class StreamingVideoManagerProfileCreatorComponent extends StreamingVideoManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE)), Translation :: get('BrowseStreamingVideo')));
		$trail->add(new Breadcrumb($this->get_url(array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE_profileS)), Translation :: get('BrowseProfiles')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateProfile')));

		$profile = new Profile();
		$form = new ProfileForm(ProfileForm :: TYPE_CREATE, $profile, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_profile();
			$this->redirect($success ? Translation :: get('ProfileCreated') : Translation :: get('ProfileNotCreated'), !$success, array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE_profileS));
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