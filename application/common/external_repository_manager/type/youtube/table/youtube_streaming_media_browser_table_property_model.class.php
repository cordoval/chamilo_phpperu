<?php
require_once dirname(__FILE__) . '/../../../component/streaming_media_browser_table/streaming_media_browser_table_property_model.class.php';
require_once dirname(__FILE__) . '/../youtube_streaming_media_connector.class.php';

class YoutubeStreamingMediaBrowserPropertyModel extends StreamingMediaBrowserPropertyModel
{

    function YoutubeStreamingMediaBrowserPropertyModel()
    {
        parent :: __construct();
        
        $youtube_properties = YoutubeStreamingMediaConnector :: get_sort_properties();
        
        foreach (YoutubeStreamingMediaConnector :: get_sort_properties() as $property)
        {
            $this->add_property(new GalleryObjectTableProperty($property));
        }
    }
}
?>