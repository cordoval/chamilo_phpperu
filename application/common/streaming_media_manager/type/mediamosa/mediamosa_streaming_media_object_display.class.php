<?php
/**
 * Description of mediamosa_streaming_media_displayclass
 *
 * @author jevdheyd
 */


class MediamosaStreamingMediaObjectDisplay  extends StreamingMediaObjectDisplay
{
        private $parent;
    
        function as_html($parent)
        {
            $this->parent = $parent;
            $object = $this->get_object();

            $html = array();
            $html[] = '<h3>' . $object->get_title() . ' (' . $object->get_duration() . ')</h3>';
            $html[] = $this->get_video_player_as_html() . '<br/>';
            $html[] = $this->get_properties_table() . '<br/>';

            return implode("\n", $html);
        }

        function get_video_player_as_html()
	{
            $connector = MediamosaStreamingMediaConnector :: get_instance($this);

            $object = $this->get_object();

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

        function get_additional_properties(){
            //get different mediafiles (+status)
            $object = $this->get_object();
            $mediafiles = $object->get_mediafiles();

            $html = array();
            $i = 1;
            
            if(is_array($mediafiles))
            {
                foreach($mediafiles as $mediafile)
                {
                    //TODO:jens -> get_link
                    $url = $this->parent->get_url(array(StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION => MediamosaStreamingMediaManager :: ACTION_VIEW_STREAMING_MEDIA, StreamingMediaManager :: PARAM_STREAMING_MEDIA_ID => $object->get_id(), MediamosaStreamingMediaManager :: PARAM_MEDIAFILE => $mediafile->get_id()));

                    $html[] = '<tr><td class="header">' . Translation :: get('Version') . ' ' .$i. '</td><td><a href="' .$url. '">' . $mediafile->get_title() . '</a></td></tr>';

                    $i++;
                }
                return '<ul>' . implode("\n",$html) . '</ul>';

            }
        }
}
?>
