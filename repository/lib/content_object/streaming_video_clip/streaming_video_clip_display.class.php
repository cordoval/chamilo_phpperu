<?php
/**
 * Description of streaming_video_clip_displayclass
 *
 * @author jevdheyd
 */

require_once Path :: get_application_path() . 'common/streaming_media_manager/type/mediamosa/mediamosa_streaming_media_connector.class.php';
require_once Path :: get_application_path() . 'common/streaming_media_manager/type/mediamosa/mediamosa_streaming_media_object.class.php';

class StreamingVideoClipDisplay extends ContentObjectDisplay
{
    private $mediamosa_object;

    function get_full_html()
    {
        $html = parent :: get_full_html();

        $video_element = $this->get_video_player_as_html();

        $additional_properties = $this->get_additional_properties();

        return str_replace(self :: DESCRIPTION_MARKER, '<div class="link_url" style="margin-top: 1em;">' . $video_element . '<br/>' .$additional_properties. '</div>' . self :: DESCRIPTION_MARKER, $html);
    }
    
    function set_mediamosa_object()
    {
        if(!$this->mediamosa_object)
        {
            $this->connector = MediamosaStreamingMediaConnector :: get_instance($this);
            $object = $this->get_content_object();
            $this->mediamosa_object = $this->connector->retrieve_mediamosa_asset($object->get_asset_id());
        }
    }

    function get_video_player_as_html()
	{
            $this->set_mediamosa_object();
            
            if($this->mediamosa_object->get_status() == StreamingMediaObject :: STATUS_AVAILABLE)
            {
                /*//see which mediafile to play
                if(Request :: get(MediamosaStreamingMediaManager :: PARAM_MEDIAFILE))
                {
                    $mediafile_id = Request :: get(MediamosaStreamingMediaManager :: PARAM_MEDIAFILE);
                }
                else
                {*/
                    $mediafile_id = $this->mediamosa_object->get_default_mediafile();
                //}

                if($mediafile_id)
                {
                    //get player
                    $output = $this->connector->mediamosa_play_proxy_request($this->mediamosa_object->get_id(), $mediafile_id);
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

       function get_additional_properties(){
            //get different mediafiles (+status)
            $mediafiles = $this->mediamosa_object->get_mediafiles();

            $html = array();
            $i = 1;
            $html[] = '<tr><td class="header">' . Translation :: get('Available versions').'</td></tr>';

            if(is_array($mediafiles))
            {
                foreach($mediafiles as $mediafile)
                {
                    //TODO:jens -> get_link
                    $html[] = '<tr><td>' . $mediafile->get_title() . '</td></tr>';

                    $i++;
                }
                return '<table class="data_table data_table_no_header">' . implode("\n",$html) . '</table>';
            }
        }
}
?>