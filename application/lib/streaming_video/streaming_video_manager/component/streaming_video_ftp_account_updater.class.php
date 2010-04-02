<?php
/**
 * @package application.streaming_video.streaming_video.component
 */
require_once dirname(__FILE__).'/../streaming_video_manager.class.php';
require_once dirname(__FILE__).'/../streaming_video_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/streaming_video_ftp_account_form.class.php';

/**
 * Component to edit an existing streaming_video_ftp_account object
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
class StreamingVideoManagerStreamingVideoFtpAccountUpdaterComponent extends StreamingVideoManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE)), Translation :: get('BrowseStreamingVideo')));
		$trail->add(new Breadcrumb($this->get_url(array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE_STREAMING_VIDEO_FTP_ACCOUNTS)), Translation :: get('BrowseStreamingVideoFtpAccounts')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateStreamingVideoFtpAccount')));

		$streaming_video_ftp_account = $this->retrieve_streaming_video_ftp_account(Request :: get(StreamingVideoManager :: PARAM_STREAMING_VIDEO_FTP_ACCOUNT));
		$form = new StreamingVideoFtpAccountForm(StreamingVideoFtpAccountForm :: TYPE_EDIT, $streaming_video_ftp_account, $this->get_url(array(StreamingVideoManager :: PARAM_STREAMING_VIDEO_FTP_ACCOUNT => $streaming_video_ftp_account->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_streaming_video_ftp_account();
			$this->redirect($success ? Translation :: get('StreamingVideoFtpAccountUpdated') : Translation :: get('StreamingVideoFtpAccountNotUpdated'), !$success, array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE_STREAMING_VIDEO_FTP_ACCOUNTS));
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