<?php
class YoutubeStreamingMediaManagerBrowserComponent extends YoutubeStreamingMediaManager
{
	function run()
	{
		$this->display_header();
		$browser = StreamingMediaComponent::factory(StreamingMediaComponent::BROWSER_COMPONENT, $this);
		$browser->run();
		$this->display_footer();
	}
}
?>