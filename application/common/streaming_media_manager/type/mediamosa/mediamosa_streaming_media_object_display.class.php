<?php
/**
 * Description of mediamosa_streaming_media_displayclass
 *
 * @author jevdheyd
 */

class MediamosaStreamingMediaObjectDisplay  extends StreamingMediaObjectDisplay
{
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
}
?>
