<?php
namespace repository\content_object\soundcloud;

use common\libraries\Utilities;
use common\libraries\Versionable;

use repository\ContentObject;

/**
 * $Id: soundcloud.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.soundcloud
 */
class Soundcloud extends ContentObject implements Versionable
{
    const CLASS_NAME = __CLASS__;

    const SOUNDCLOUD_TRACK_API_URI = 'http://api.soundcloud.com/tracks/%s';
    const SOUNDCLOUD_PLAYER_URI = 'http://player.soundcloud.com/player.swf?url=%s&secret_url=false';

    static function get_type_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    function get_track_api_uri()
    {
        return sprintf(self ::SOUNDCLOUD_TRACK_API_URI, $this->get_synchronization_data()->get_external_repository_object_id());
    }

    function get_track_player_uri()
    {
        return sprintf(self :: SOUNDCLOUD_PLAYER_URI, urlencode($this->get_track_api_uri()));
    }
}
?>