<?php
require_once dirname(__FILE__) . '/youtube_streaming_media_connector.class.php';

class YoutubeStreamingMediaManager extends StreamingMediaManager
{
	const PARAM_FEED_TYPE = 'feed';
	const FEED_TYPE_GENERAL = 1;
	const FEED_TYPE_MYVIDEOS = 2;
	
	
    function get_application_component_path()
    {
        return Path :: get_application_library_path() . 'streaming_media_manager/type/youtube/component/';
    }

    function count_streaming_media_objects($condition)
    {
        $connector = YoutubeStreamingMediaConnector :: get_instance($this);
        return $connector->count_youtube_video($condition);
    }

    function retrieve_streaming_media_objects($condition, $order_property, $offset, $count)
    {
        $connector = YoutubeStreamingMediaConnector :: get_instance($this);
        return $connector->get_youtube_video($condition, $order_property, $offset, $count);
    }

    function get_sort_properties()
    {
        return YoutubeStreamingMediaConnector :: get_sort_properties();
    }

    function support_sorting_direction()
    {
        return false;
    }

    function translate_search_query($query)
    {
        return YoutubeStreamingMediaConnector :: translate_search_query($query);
    }

    function get_menu_items()
    {
        $menu_items = array();
        $browser = array();       
        $browser['title'] = Translation :: get('YoutubeBrowse');
        $browser['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_TYPE_GENERAL), array(StreamingMediaSearchForm::PARAM_SIMPLE_SEARCH_QUERY));
        $browser['class'] = 'home';
        $menu_items[] = $browser;
        
        $my_videos = array();
        $my_videos['title'] = Translation :: get('MyVideos');
        $my_videos['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_TYPE_MYVIDEOS), array(StreamingMediaSearchForm::PARAM_SIMPLE_SEARCH_QUERY));
        $my_videos['class'] = 'user';
        $menu_items[] = $my_videos;
        
        return $menu_items;
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