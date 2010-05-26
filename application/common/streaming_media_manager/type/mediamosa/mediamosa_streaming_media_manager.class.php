<?php

/**
 * Description of mediamosa_streaming_media_manager
 *
 * @author jevdheyd
 */

require_once dirname(__FILE__) . '/mediamosa_streaming_media_connector.class.php';

class MediamosaStreamingMediaManager extends StreamingMediaManager{

    function MediamosaStreamingVideoManager($application)
    {
        parent :: __construct($application);
    }

    function get_application_component_path()
    {
        return Path :: get_application_library_path() . 'streaming_media_manager/type/mediamosa/component/';
    }

    function count_streaming_media_objects($condition)
    {

    }

    function retrieve_streaming_media_objects($condition, $order_property, $offset, $count)
    {
        $connector = MediamosaStreamingMediaConnector::get_instance($this);
        $connector->get_mediamosa_assets($condition, $order_property, $offset, $count);
    }

    function translate_search_query($query)
    {}

    function get_menu_items(){}

    function get_streaming_media_object_viewing_url($object){}

    function retrieve_streaming_media_object($id)
    {
        $connector = MediamosaStreamingMediaConnector::get_instance($this);
        $connector->get_mediamosa_asset($id);
    }

    function delete_streaming_media_object($id){}

    function export_streaming_media_object($id){}

    function is_editable($id)
    {
        $connector = MediamosaStreamingMediaConnector :: get_instance($this);
    	return $connector->is_editable($id);
    }

    function run()
    {
         $parent = $this->get_parameter(StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION);

        switch ($parent)
        {
            case StreamingMediaManager :: ACTION_BROWSE_STREAMING_MEDIA :
                $component = $this->create_component('Browser', $this);
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
