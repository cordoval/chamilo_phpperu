<?php
class YoutubeStreamingMediaManagerSelecterComponent extends YoutubeStreamingMediaManager
{
	function run()
	{
		$selecter = StreamingMediaComponent::factory(StreamingMediaComponent::SELECTER_COMPONENT, $this);
		
		$selecter->run();
	}
}
?>