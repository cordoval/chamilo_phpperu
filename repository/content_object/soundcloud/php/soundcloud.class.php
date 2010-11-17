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
    const PROPERTY_TRACK_ID = 'track_id';
    const CLASS_NAME = __CLASS__;

    const SOUNDCLOUD_TRACK_API_URI = 'http://api.soundcloud.com/tracks/%s';
    const SOUNDCLOUD_PLAYER_URI = 'http://player.soundcloud.com/player.swf?url=%s&secret_url=false';

    static function get_type_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    function get_track_id()
    {
        return $this->get_additional_property(self :: PROPERTY_TRACK_ID);
    }

    function set_track_id($track_id)
    {
        return $this->set_additional_property(self :: PROPERTY_TRACK_ID, $track_id);
    }

    function get_track_api_uri()
    {
        return sprintf(self ::SOUNDCLOUD_TRACK_API_URI, $this->get_track_id());
    }

    function get_track_player_uri()
    {
        return sprintf(self :: SOUNDCLOUD_PLAYER_URI, urlencode($this->get_track_api_uri()));
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_TRACK_ID);
    }
}
?>