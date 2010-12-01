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
    
    static function get_type_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

}
?>