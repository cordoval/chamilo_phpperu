<?php
namespace common\extensions\video_conferencing_manager\implementation\bbb;

use common\extensions\video_conferencing_manager\VideoConferencingObject;


class BbbVideoConferencingObject extends VideoConferencingObject
{
    const OBJECT_TYPE = 'bbb';
    
    const PROPERTY_ATTENDEE_PW = 'attendee_pw';
    const PROPERTY_MODERATOR_PW = 'moderator_pw';
    const PROPERTY_WELCOME = 'welcome';
    const PROPERTY_LOGOUT_URL = 'logout_url';
    const PROPERTY_MAX_PARTICIPANTS = 'max_participants';

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_ATTENDEE_PW, self :: PROPERTY_MODERATOR_PW, self :: PROPERTY_WELCOME, self :: PROPERTY_LOGOUT_URL, self :: PROPERTY_MAX_PARTICIPANTS));
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
    
    function set_welcome($welcome)
    {
        return $this->set_default_property(self :: PROPERTY_WELCOME, $welcome);
    }

    function get_welcome()
    {
        return $this->get_default_property(self :: PROPERTY_WELCOME);
    }
    
    function set_logout_url($logout_url)
    {
        return $this->set_default_property(self :: PROPERTY_LOGOUT_URL, $logout_url);
    }

    function get_logout_url()
    {
        return $this->get_default_property(self :: PROPERTY_LOGOUT_URL);
    }
    
    function set_max_participants($max_participants)
    {
        return $this->set_default_property(self :: PROPERTY_MAX_PARTICIPANTS, $max_participants);
    }

    function get_max_participants()
    {
        return $this->get_default_property(self :: PROPERTY_MAX_PARTICIPANTS);
    }
    
    static function get_object_type()
    {
        return self :: OBJECT_TYPE;
    }
}
?>