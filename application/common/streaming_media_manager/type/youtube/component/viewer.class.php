<?php
class YoutubeStreamingMediaManagerViewerComponent extends YoutubeStreamingMediaManager
{
	function run()
	{
		$viewer = StreamingMediaComponent::factory(StreamingMediaComponent::VIEWER_COMPONENT, $this);
		
		$viewer->run();
	}
}
?>