<?php
class YoutubeStreamingMediaManagerExporterComponent extends YoutubeStreamingMediaManager
{
	function run()
	{
		$exporter = StreamingMediaComponent::factory(StreamingMediaComponent::EXPORTER_COMPONENT, $this);
		
		$exporter->run();
	}
	
}
?>