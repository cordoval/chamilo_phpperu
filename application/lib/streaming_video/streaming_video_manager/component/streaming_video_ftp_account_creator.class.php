<?php
/**
 * @package application.streaming_video.streaming_video.component
 */
require_once dirname(__FILE__).'/../streaming_video_manager.class.php';
require_once dirname(__FILE__).'/../streaming_video_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/streaming_video_ftp_account_form.class.php';

/**
 * Component to create a new streaming_video_ftp_account object
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
class StreamingVideoManagerStreamingVideoFtpAccountCreatorComponent extends StreamingVideoManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE)), Translation :: get('BrowseStreamingVideo')));
		$trail->add(new Breadcrumb($this->get_url(array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE_STREAMING_VIDEO_FTP_ACCOUNTS)), Translation :: get('BrowseStreamingVideoFtpAccounts')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateStreamingVideoFtpAccount')));

		$streaming_video_ftp_account = new StreamingVideoFtpAccount();
		$form = new StreamingVideoFtpAccountForm(StreamingVideoFtpAccountForm :: TYPE_CREATE, $streaming_video_ftp_account, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_streaming_video_ftp_account();
			$this->redirect($success ? Translation :: get('StreamingVideoFtpAccountCreated') : Translation :: get('StreamingVideoFtpAccountNotCreated'), !$success, array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE_STREAMING_VIDEO_FTP_ACCOUNTS));
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