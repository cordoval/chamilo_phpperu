<?php
namespace repository\content_object\bbb_meeting;

use common\libraries\Utilities;
use common\libraries\Versionable;

use repository\ContentObject;

/**
 * $Id: bbb_meeting.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.soundcloud
 */
class BbbMeeting extends ContentObject implements Versionable
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_MODERATOR_PW = 'moderator_pw';
    
    static function get_type_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
    
 	function get_moderator_pw()
    {
        return $this->get_additional_property(self :: PROPERTY_MODERATOR_PW);
    }

    function set_moderator_pw($moderator_pw)
    {
        return $this->set_additional_property(self :: PROPERTY_MODERATOR_PW, $moderator_pw);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_MODERATOR_PW);
    }

}
?>