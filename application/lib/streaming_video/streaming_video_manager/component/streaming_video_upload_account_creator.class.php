<?php
/**
 * @package application.streaming_video.streaming_video.component
 */
require_once dirname(__FILE__).'/../streaming_video_manager.class.php';
require_once dirname(__FILE__).'/../streaming_video_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/upload_account_form.class.php';

/**
 * Component to create a new upload_account object
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
class StreamingVideoManagerUploadAccountCreatorComponent extends StreamingVideoManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE)), Translation :: get('BrowseStreamingVideo')));
		$trail->add(new Breadcrumb($this->get_url(array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE_upload_accountS)), Translation :: get('BrowseUploadAccounts')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateUploadAccount')));

		$upload_account = new UploadAccount();
		$form = new UploadAccountForm(UploadAccountForm :: TYPE_CREATE, $upload_account, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_upload_account();
			$this->redirect($success ? Translation :: get('UploadAccountCreated') : Translation :: get('UploadAccountNotCreated'), !$success, array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE_upload_accountS));
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