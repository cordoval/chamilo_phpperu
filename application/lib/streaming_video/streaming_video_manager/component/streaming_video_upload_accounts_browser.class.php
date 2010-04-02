<?php
/**
 * @package application.streaming_video.streaming_video.component
 */

require_once dirname(__FILE__).'/../streaming_video_manager.class.php';
require_once dirname(__FILE__).'/../streaming_video_manager_component.class.php';

/**
 * streaming_video component which allows the user to browse his upload_accounts
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
class StreamingVideoManagerUploadAccountsBrowserComponent extends StreamingVideoManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE)), Translation :: get('BrowseStreamingVideo')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseUploadAccounts')));

		$this->display_header($trail);

		echo '<a href="' . $this->get_create_upload_account_url() . '">' . Translation :: get('CreateUploadAccount') . '</a>';
		echo '<br /><br />';

		$upload_accounts = $this->retrieve_upload_accounts();
		while($upload_account = $upload_accounts->next_result())
		{
			echo '<div style="border: 1px solid grey; padding: 5px;">';
			dump($upload_account);
			echo '<br /><a href="' . $this->get_update_upload_account_url($upload_account). '">' . Translation :: get('UpdateUploadAccount') . '</a>';
			echo ' | <a href="' . $this->get_delete_upload_account_url($upload_account) . '">' . Translation :: get('DeleteUploadAccount') . '</a>';
			echo '</div><br /><br />';
		}

		$this->display_footer();
	}

}
?>