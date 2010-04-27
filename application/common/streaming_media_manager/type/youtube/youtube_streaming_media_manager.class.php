<?php
require_once dirname(__FILE__) . '/youtube_streaming_media_connector.class.php';

class YoutubeStreamingMediaManager extends StreamingMediaManager
{
	function get_application_component_path()
	{
		return Path :: get_application_library_path() . 'streaming_media_manager/type/youtube/component/';
	}
	
	function count_streaming_media_objects($condition)
	{
		$connector = YoutubeStreamingMediaConnector::get_instance($this);
		return $connector->count_youtube_video($condition);
	}
	
	function retrieve_streaming_media_objects($condition, $order_property, $offset, $count)
	{
		$connector = YoutubeStreamingMediaConnector::get_instance($this);
		return $connector->get_youtube_video($condition, $order_property, $offset, $count);
	}
	
	
	function is_ready_to_be_used()
    {
//        $action = $this->get_parameter(self :: PARAM_STREAMING_MEDIA_MANAGER_ACTION);
//
//        return self :: any_object_selected() && ($action == self :: ACTION_PUBLISHER);
		return false;
    }
	
	function run()
	{
		$parent = $this->get_parameter(StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION);
        
        switch ($parent)
        {
            case StreamingMediaManager :: ACTION_VIEW_STREAMING_MEDIA :
                $component = $this->create_component('Viewer');
                break;
            case StreamingMediaManager :: ACTION_EXPORT_STREAMING_MEDIA :
                $component = $this->create_component('Exporter');
                break;
  			case StreamingMediaManager :: ACTION_IMPORT_STREAMING_MEDIA :
  				$component = $this->create_component('Importer');
  				break;
  			case StreamingMediaManager :: ACTION_BROWSE_STREAMING_MEDIA :
  				$component = $this->create_component('Browser', $this);
  				break;   
  			case StreamingMediaManager :: ACTION_DOWNLOAD_STREAMING_MEDIA :
  				$component = $this->create_component('Downloader');
  				break; 
  			case StreamingMediaManager :: ACTION_UPLOAD_STREAMING_MEDIA :
  				$component = $this->create_component('Uploader');
  				break;
            default :
                $component = $this->create_component('Browser', $this);
                $this->set_parameter(StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION, StreamingMediaManager :: ACTION_BROWSE_STREAMING_MEDIA);
                break;
        }
        
        $component->run();
	}
}
?>