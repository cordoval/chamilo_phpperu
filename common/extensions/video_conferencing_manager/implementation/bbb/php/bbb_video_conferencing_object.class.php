<?php
namespace common\extensions\video_conferencing_manager\implementation\bbb;

use common\extensions\video_conferencing_manager\VideoConferencingObject;


class BbbVideoConferencingObject extends VideoConferencingObject
{
    const OBJECT_TYPE = 'bbb';
    
    const PROPERTY_ATTENDEE_PW = 'attendee_pw';
    const PROPERTY_MODERATOR_PW = 'moderator_pw';

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_ATTENDEE_PW, self :: PROPERTY_MODERATOR_PW));
    }
    
    function set_attendee_pw($attendee_pw)
    {
        return $this->set_default_property(self :: PROPERTY_ATTENDEE_PW, $attendee_pw);
    }

    function get_attendee_pw()
    {
        return $this->get_default_property(self :: PROPERTY_ATTENDEE_PW);
    }
    
    function set_moderator_pw($moderator_pw)
    {
        return $this->set_default_property(self :: PROPERTY_MODERATOR_PW, $moderator_pw);
    }

    function get_moderator_pw()
    {
        return $this->get_default_property(self :: PROPERTY_MODERATOR_PW);
    }
    
    static function get_object_type()
    {
        return self :: OBJECT_TYPE;
    }
}
?>