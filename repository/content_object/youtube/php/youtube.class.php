<?php
namespace repository\content_object\youtube;

use common\libraries\Utilities;
use common\libraries\Versionable;

use repository\ContentObject;

/**
 * $Id: youtube.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.youtube
 */
class Youtube extends ContentObject implements Versionable
{
    const CLASS_NAME = __CLASS__;

    const YOUTUBE_PLAYER_URI = 'http://www.youtube.com/v/%s';

    static function get_type_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    function get_video_url()
    {
        return sprintf(self :: YOUTUBE_PLAYER_URI, $this->get_synchronization_data()->get_external_repository_object_id());
    }
}
?>