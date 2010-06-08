<?php
/**
 * Description of streaming_video_clip_displayclass
 *
 * @author jevdheyd
 */
class StreamingVideoClipDisplay extends ContentObjectDisplay
{
    const PARAM_PROFILE_NAME = 'ovis_profile';

    const SESSION_KEY_PROFILE_NAME = 'ovis_profile';

    const OBJECT_ID = 'ovis_player';

    function get_full_html()
    {
        $html = parent :: get_full_html();
        $object = $this->get_content_object();
        $conversion_state = $object->get_conversion_state();

        //if the clip is ready for streaming
        if($conversion_state == StreamingVideoClip :: STATE_PUBLIC)
        {
            //get different transcoding profiles
           $svdm = StreamingVideoDataManager :: get_instance();
           $result = $svdm->retrieve_transcoding_profiles();

           $profiles = array();

           while($profile = $result->next_result())
           {
               $profiles[] = $profile;

           }

           if($p = self :: find_profile(Request :: get(self :: PARAM_PROFILE_NAME), $profiles))
           {
                Session::register(self :: PARAM_PROFILE_NAME, $p->get_name());
                $profile = $p;
           }
           elseif ($p = self :: find_profile(Session :: retrieve(self :: PARAM_PROFILE_NAME), $profiles))
           {
                $profile = $p;
           }
           else
           {
               $profile = $profiles->next_result();
           }
           

        }
        else
        {
            $state_html = '<div class="video_clip_state video_clip_state' . $conversion_state . '">' . "\r\n"
                    . htmlspecialchars(self::translate_conversion_state($conversion_state)) . "\r\n"
                    . '</div>' . "\r\n";
            $html = str_replace(self :: TITLE_MARKER, $state_html . self :: TITLE_MARKER, $html);
        }

        return $html;
    }

    static function translate_conversion_state($state)
    {
        switch ($state)
        {
            case StreamingVideoClip :: STATE_PUBLIC: return Translation::get('VideoClipStatePublic');
            case StreamingVideoClip :: STATE_QUEUED: return Translation::get('VideoClipStateQueuedForConversion');
            case StreamingVideoClip :: STATE_TRANSCODING: return Translation::get('VideoClipStateConversionInProgress');
            case StreamingVideoClip :: STATE_ERRONEOUS: return Translation::get('VideoClipStateConversionFailed');
	}
    }

    private static function find_profile($name, $profiles)
    {
       foreach($profiles as $profile)
       {
           if($profile->get_name())
           {
               return $profile;
           }
       }

       return null;
    
    }
}
?>