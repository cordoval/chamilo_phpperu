<?php
/**
 * @package application.streaming_video.streaming_video.component
 */
require_once dirname(__FILE__).'/../streaming_video_manager.class.php';
require_once dirname(__FILE__).'/../streaming_video_manager_component.class.php';

/**
 * StreamingVideo component which allows the user to browse the streaming_video application
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
class StreamingVideoManagerBrowserComponent extends StreamingVideoManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseStreamingVideo')));

		$this->display_header($trail);

		echo '<br /><a href="' . $this->get_browse_parameters_url() . '">' . Translation :: get('BrowseParameters') . '</a>';
		echo '<br /><a href="' . $this->get_browse_profiles_url() . '">' . Translation :: get('BrowseProfiles') . '</a>';
		echo '<br /><a href="' . $this->get_browse_upload_accounts_url() . '">' . Translation :: get('BrowseUploadAccounts') . '</a>';
		echo '<br /><a href="' . $this->get_browse_streaming_video_ftp_accounts_url() . '">' . Translation :: get('BrowseStreamingVideoFtpAccounts') . '</a>';
		echo '<br /><a href="' . $this->get_browse_streaming_video_transcodings_url() . '">' . Translation :: get('BrowseStreamingVideoTranscodings') . '</a>';

		$this->display_footer();
	}

}
?>