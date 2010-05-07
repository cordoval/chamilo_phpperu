<?php
class YoutubeStreamingMediaManagerBrowserComponent extends YoutubeStreamingMediaManager
{
	function run()
	{
		if (Request :: get(YoutubeStreamingMediaManager::PARAM_FEED_TYPE) == YoutubeStreamingMediaManager::FEED_STANDARD_TYPE)
		{
			$this->set_parameter(YoutubeStreamingMediaManager::PARAM_FEED_IDENTIFIER, Request :: get(YoutubeStreamingMediaManager::PARAM_FEED_IDENTIFIER));
		}
		$browser = StreamingMediaComponent::factory(StreamingMediaComponent::BROWSER_COMPONENT, $this);
		
		$browser->run();
	}
}
?>