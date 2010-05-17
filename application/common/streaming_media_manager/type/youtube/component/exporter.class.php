<?php
class YoutubeStreamingMediaManagerExporterComponent extends YoutubeStreamingMediaManager
{
	function run()
	{
		$exporter = StreamingMediaComponent::factory(StreamingMediaComponent::EXPORTER_COMPONENT, $this);
		
		$exporter->run();
	}
	
	function export_streaming_media_object($object)
	{
		$success = parent :: export_streaming_media_object($object);
		if ($success)
		{
			$parameters = $this->get_parameters();
			$parameters [StreamingMediaManager::PARAM_STREAMING_MEDIA_MANAGER_ACTION] = StreamingMediaManager::ACTION_BROWSE_STREAMING_MEDIA;
			$parameters[YoutubeStreamingMediaManager::PARAM_FEED_TYPE] = YoutubeStreamingMediaManager::FEED_TYPE_MYVIDEOS;
			$this->redirect(Translation :: get('ExportSuccesfull'), false, $parameters);
		}
		else
		{
			$parameters = $this->get_parameters();
			$parameters [StreamingMediaManager::PARAM_STREAMING_MEDIA_MANAGER_ACTION] = StreamingMediaManager::ACTION_EXPORT_STREAMING_MEDIA;
			$this->redirect(Translation :: get('ExportFailed'), true, $parameters);
		}
	}
	
}
?>