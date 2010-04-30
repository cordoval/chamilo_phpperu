<?php
class YoutubeStreamingMediaManagerImporterComponent extends YoutubeStreamingMediaManager
{
	function run()
	{
		$importer = StreamingMediaComponent::factory(StreamingMediaComponent::IMPORTER_COMPONENT, $this);
		
		$importer->run();
	}
	
	function import_streaming_media_object($object)
	{
		$youtube = ContentObject::factory(Youtube::get_type_name());       
        $youtube->set_title($object->get_title());
        $youtube->set_description($object->get_description());
        $youtube->set_url('http://www.youtube.com/watch?v=' . $object->get_id());
        $youtube->set_height(344);
        $youtube->set_width(425);
        $youtube->set_owner_id($this->get_user_id());
        if ($youtube->create())
        {
        	$parameters = $this->get_parameters();
	        $parameters [Application::PARAM_ACTION] = RepositoryManager::ACTION_BROWSE_CONTENT_OBJECTS;
	        $this->redirect(Translation :: get('ImportSuccesfull'), false, $parameters, array(StreamingMediaManager::PARAM_TYPE, StreamingMediaManager::PARAM_STREAMING_MEDIA_MANAGER_ACTION));
//        	Redirect :: link(RepositoryManager::APPLICATION_NAME, array(RepositoryManager:: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $youtube->get_id(), RepositoryManager :: PARAM_CATEGORY_ID => $youtube->get_parent_id()));
        }
        else 
        {
	        $parameters = $this->get_parameters();
	        $parameters [StreamingMediaManager::PARAM_STREAMING_MEDIA_MANAGER_ACTION] = StreamingMediaManager::ACTION_VIEW_STREAMING_MEDIA;
	        $parameters [StreamingMediaManager::PARAM_STREAMING_MEDIA_ID] = $object->get_id(); 
	        $this->redirect(Translation :: get('ImportFailled'), true, $parameters);
        }
        
	}
}
?>