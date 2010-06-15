<?php
/**
 * Description of streaming_video_clip_displayclass
 *
 * @author jevdheyd
 */

require_once Path :: get_application_path() . '/common/streaming_video/type/mediamosa/mediamosa_streaming_video_connector.class.php';

class StreamingVideoClipDisplay extends ContentObjectDisplay
{
    private $mediamosa_object;

    function get_full_html()
    {
        $html = parent :: get_full_html();
        $object = $this->get_content_object();
        
        $html = array();
        $html[] = '<h3>' . $object->get_title() . ' (' . Utilities :: format_seconds_to_minutes($object->get_duration()) . ')</h3>';
        $html[] = $this->get_video_player_as_html() . '<br/>';
        $html[] = $this->get_properties_table() . '<br/>';

        return implode("\n", $html);
    }

    function get_video_player_as_html()
    {
        xdebug_break();
        
        $object = $this->get_mediamosa_object();

        if($object->get_status() == StreamingMediaObject :: STATUS_AVAILABLE)
        {
            //see which mediafile to play
            if(Request :: get(MediamosaStreamingMediaManager :: PARAM_MEDIAFILE))
            {
                $mediafile_id = Request :: get(MediamosaStreamingMediaManager :: PARAM_MEDIAFILE);
            }
            else
            {
                $mediafile_id = $object->get_default_mediafile();
            }

            if($mediafile_id)
            {
                //get player
                $output = $connector->mediamosa_play_proxy_request($object->get_id(), $mediafile_id);
            }
            else{
                $output = '';
            }
        }
        else
        {
            $output = Translation :: get('video_not_available');
        }
        return $output;
    }

    function get_properties_table()
    {
        //get different mediafiles (+status)
        $mediamosa_object = $this->get_mediamosa_object();
        $mediafiles = $mediamosa_object->get_mediafiles();

        $html[] = array();

        if(is_array($mediafiles))
        {
            foreach($mediafiles as $mediafile)
            {
                //TODO:jens -> get_link
                $html[] = '<li><a href="">' . $mediafile->get_title() . '</a></li>';
            }
            return '<ul>' . implode("\n", $html) . '</ul>';
        }
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
   
}
?>