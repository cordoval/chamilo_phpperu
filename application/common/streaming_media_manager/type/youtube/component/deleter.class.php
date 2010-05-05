<?php
class YoutubeStreamingMediaManagerDeleterComponent extends YoutubeStreamingMediaManager
{
	function run()
	{
		$deleter = StreamingMediaComponent::factory(StreamingMediaComponent::DELETER_COMPONENT, $this);
		$deleter->run();
	}
	
	function delete_streaming_media_object($id)
	{
		$success = parent :: delete_streaming_media_object($id);
		if ($success)
		{
			$parameters = $this->get_parameters();
			$parameters [StreamingMediaManager::PARAM_STREAMING_MEDIA_MANAGER_ACTION] = StreamingMediaManager::ACTION_BROWSE_STREAMING_MEDIA;
			$this->redirect(Translation :: get('DeleteSuccesfull'), false, $parameters);
		}
		else
		{
			$parameters = $this->get_parameters();
			$parameters [StreamingMediaManager::PARAM_STREAMING_MEDIA_MANAGER_ACTION] = StreamingMediaManager::ACTION_VIEW_STREAMING_MEDIA;
			$parameters [StreamingMediaManager::PARAM_STREAMING_MEDIA_ID] = $id;
			$this->redirect(Translation :: get('DeleteFailed'), true, $parameters);
		}
	}
}
?>