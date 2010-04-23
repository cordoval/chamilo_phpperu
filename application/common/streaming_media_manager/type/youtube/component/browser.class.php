<?php
class YoutubeStreamingMediaManagerBrowserComponent extends YoutubeStreamingMediaManager
{
	function run()
	{
		$browser = StreamingMediaComponent::factory(StreamingMediaComponent::BROWSER_COMPONENT, $this);
		$browser->run();
	}
}
?>