<?php
/**
 * Description of streaming_video_clip_cue_pointclass
 *
 * @author jevdheyd
 */
class StreamingVideoClipCuePoint extends ContentObject 
{
    const PROPERTY_START_TIME = 'start_time';
	const CLASS_NAME = __CLASS__;

	static function get_type_name() 
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
	
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