<?php
/**
 * @package application.streaming_video.streaming_video.component
 */

require_once dirname(__FILE__).'/../streaming_video_manager.class.php';
require_once dirname(__FILE__).'/../streaming_video_manager_component.class.php';

/**
 * streaming_video component which allows the user to browse his streaming_video_ftp_accounts
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
class StreamingVideoManagerStreamingVideoFtpAccountsBrowserComponent extends StreamingVideoManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE)), Translation :: get('BrowseStreamingVideo')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseStreamingVideoFtpAccounts')));

		$this->display_header($trail);

		echo '<a href="' . $this->get_create_streaming_video_ftp_account_url() . '">' . Translation :: get('CreateStreamingVideoFtpAccount') . '</a>';
		echo '<br /><br />';

		$streaming_video_ftp_accounts = $this->retrieve_streaming_video_ftp_accounts();
		while($streaming_video_ftp_account = $streaming_video_ftp_accounts->next_result())
		{
			echo '<div style="border: 1px solid grey; padding: 5px;">';
			dump($streaming_video_ftp_account);
			echo '<br /><a href="' . $this->get_update_streaming_video_ftp_account_url($streaming_video_ftp_account). '">' . Translation :: get('UpdateStreamingVideoFtpAccount') . '</a>';
			echo ' | <a href="' . $this->get_delete_streaming_video_ftp_account_url($streaming_video_ftp_account) . '">' . Translation :: get('DeleteStreamingVideoFtpAccount') . '</a>';
			echo '</div><br /><br />';
		}

		$this->display_footer();
	}

}
?>