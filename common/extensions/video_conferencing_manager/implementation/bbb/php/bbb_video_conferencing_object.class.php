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
    const PROPERTY_START_TIME = 'start_time';
    const PROPERTY_END_TIME = 'end_time';
    const PROPERTY_RUNNING = 'running';
    const PROPERTY_MODERATORS = 'moderators';
    const PROPERTY_VIEWERS = 'viewers';
    const PROPERTY_FORCIBLY_ENDED = 'forcibly_ended';

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(
                self :: PROPERTY_RUNNING, self :: PROPERTY_MODERATORS, self :: PROPERTY_VIEWERS, self :: PROPERTY_END_TIME, self :: PROPERTY_START_TIME, self :: PROPERTY_ATTENDEE_PW, self :: PROPERTY_MODERATOR_PW, self :: PROPERTY_WELCOME, 
                self :: PROPERTY_LOGOUT_URL, self :: PROPERTY_MAX_PARTICIPANTS, self :: PROPERTY_FORCIBLY_ENDED));
    }
    
	function set_forcibly_ended($forcibly_ended)
    {
        return $this->set_default_property(self :: PROPERTY_FORCIBLY_ENDED, $forcibly_ended);
    }

    function get_forcibly_ended()
    {
        return $this->get_default_property(self :: PROPERTY_FORCIBLY_ENDED);
    }  
    
    function is_joinable()
    {
    	return ! $this->get_forcibly_ended();
    }
    
    function is_endable()
    {
    	return $this->is_joinable();
    }
    
    function set_start_time($start_time)
    {
        return $this->set_default_property(self :: PROPERTY_START_TIME, $start_time);
    }

    function get_start_time()
    {
        return $this->get_default_property(self :: PROPERTY_START_TIME);
    }

    function set_end_time($end_time)
    {
        return $this->set_default_property(self :: PROPERTY_END_TIME, $end_time);
    }

    function get_end_time()
    {
        return $this->get_default_property(self :: PROPERTY_END_TIME);
    }
    
    function get_viewers()
    {
    	return $this->get_default_property(self :: PROPERTY_VIEWERS);
    }

    function get_moderators()
    {
    	return $this->get_default_property(self :: PROPERTY_MODERATORS);
    }
    
    function add_moderator($attendee)
    {
    	$moderator = $this->get_moderators();
    	$moderator[] = $attendee;
    }
    
    function add_viewer($attendee)
    {
    	$viewer = $this->get_viewers();
    	$viewer[] = $attendee;
    }
    
    function set_running($running)
    {
        return $this->set_default_property(self :: PROPERTY_RUNNING, $running);
    }

    function get_running()
    {
        return $this->get_default_property(self :: PROPERTY_RUNNING);
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