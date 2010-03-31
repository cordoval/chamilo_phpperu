<?php
/**
 * Description of streaming_video_clip_cue_pointclass
 *
 * @author jevdheyd
 */
class StreamingVideoClipCuePoint extends ContentObject {
    const PROPERTY_START_TIME = 'start_time';

    function get_start_time ()
    {
            return $this->get_additional_property(self :: PROPERTY_START_TIME);
    }

    function set_start_time ($start_time)
    {
            return $this->set_additional_property(self :: PROPERTY_START_TIME, $start_time);
    }

    /*function is_versionable()
    {
            return false;
    }

    function is_master_type()
    {
            return false;
    }*/
}
?>