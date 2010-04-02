<?php
/**
 * @package application.streaming_video.streaming_video.component
 */
require_once dirname(__FILE__).'/../streaming_video_manager.class.php';
require_once dirname(__FILE__).'/../streaming_video_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/upload_account_form.class.php';

/**
 * Component to edit an existing upload_account object
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
class StreamingVideoManagerUploadAccountUpdaterComponent extends StreamingVideoManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE)), Translation :: get('BrowseStreamingVideo')));
		$trail->add(new Breadcrumb($this->get_url(array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE_upload_accountS)), Translation :: get('BrowseUploadAccounts')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateUploadAccount')));

		$upload_account = $this->retrieve_upload_account(Request :: get(StreamingVideoManager :: PARAM_upload_account));
		$form = new UploadAccountForm(UploadAccountForm :: TYPE_EDIT, $upload_account, $this->get_url(array(StreamingVideoManager :: PARAM_upload_account => $upload_account->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_upload_account();
			$this->redirect($success ? Translation :: get('UploadAccountUpdated') : Translation :: get('UploadAccountNotUpdated'), !$success, array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE_upload_accountS));
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