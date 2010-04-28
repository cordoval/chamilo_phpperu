<?php
class YoutubeStreamingMediaManagerBrowserComponent extends YoutubeStreamingMediaManager
{
	function run()
	{
		$this->set_parameter(YoutubeStreamingMediaManager::PARAM_FEED_TYPE, Request :: get(YoutubeStreamingMediaManager::PARAM_FEED_TYPE));
		$browser = StreamingMediaComponent::factory(StreamingMediaComponent::BROWSER_COMPONENT, $this);
		
		$browser->run();
	}
}
?>