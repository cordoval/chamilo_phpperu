<?php
/*
 * @author jevdheyd
 */
class StreamingVideoClip extends ContentObject
{
    const STREAMING_VIDEO_ADMIN_SETTING = 'streaming_video';
    const STREAMING_VIDEO_ADMIN_SETTING_VALUE = 'streaming_video_value';

    const PROPERTY_APPLICATION = 'application';

    static function get_additional_property_names()
    {
            return array(self :: PROPERTY_APPLICATION);
    }

    function get_application ()
    {
            return $this->get_additional_property(self :: PROPERTY_APPLICATION);
    }

    function set_application ($application)
    {
        return $this->set_additional_property(self :: PROPERTY_APPLICATION, $application);
    }
}
?>