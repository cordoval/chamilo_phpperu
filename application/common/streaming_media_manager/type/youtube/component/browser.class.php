<?php
require_once dirname(__FILE__) . '/../../../component/browser.class.php';
class YoutubeStreamingMediaManagerBrowserComponent extends StreamingMediaManagerBrowserComponent
{
	function run()
	{
		$this->display_header();
		echo('in run of YoutubeStreaming');
		$this->display_footer();
	}
}
?>