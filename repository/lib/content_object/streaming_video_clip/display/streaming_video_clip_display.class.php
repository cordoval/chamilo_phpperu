<?php
/**
 * Description of streaming_video_clip_displayclass
 *
 * @author jevdheyd
 */
require_once Path :: get_application_path() . 'common/streaming_media_manager/type/mediamosa/mediamosa_streaming_media_connector.class.php';
require_once Path :: get_application_path() . 'common/streaming_media_manager/type/mediamosa/mediamosa_streaming_media_object.class.php';


class StreamingVideoClipDisplay extends ComplexDisplay

    private $mediamosa_object;

    const ACTION_VIEW_MEDIAFILE = 'mediafile_id';

    function run()
    {
        default :
            $component = $this->create_component('Viewer');
    }

    function get_mediamosa_object()
    {
        if(!$this->mediamosa_object)
        {
            $connector = MediamosaStreamingMediaConnector :: get_instance($this);
            $object = $this->get_content_object();
            $this->mediamosa_object = $connector->retrieve_mediamosa_asset($object->get_asset_id());
        }
        return $this->mediamosa_object;
    }

    function get_application_component_path()
    {
		return dirname(__FILE__) . '/component/';
    }
}
?>
