<?php
namespace repository\content_object\soundcloud;

use common\libraries\Text;

use repository\ContentObjectDisplay;

/**
 * $Id: soundcloud_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.soundcloud
 */
class SoundcloudDisplay extends ContentObjectDisplay
{
    const SOUNDCLOUD_TRACK_API_URI = 'http://api.soundcloud.com/tracks/';
    const SOUNDCLOUD_PLAYER_URI = 'http://player.soundcloud.com/player.swf?url=%s&secret_url=false';

    function get_track_element($width = '100%', $height = '81')
    {
        $object = $this->get_content_object();

        $preview_url = urlencode($object->get_track_api_uri());

        $html = array();
        $html[] = '<object height="' . $height . '" width="' . $width . '">';
        $html[] = '<param name="movie" value="' . $object->get_track_player_uri() . '"></param>';
        $html[] = '<param name="allowscriptaccess" value="always"></param>';
        $html[] = '<embed allowscriptaccess="always" height="81" src="' . $object->get_track_player_uri() . '" type="application/x-shockwave-flash" width="100%"></embed>';
        $html[] = '</object>';

        return implode("\n", $html);
    }

    function get_description()
    {
        $html = parent :: get_description();
        $object = $this->get_content_object();

        $track_element = $this->get_track_element();

        return str_replace(self :: DESCRIPTION_MARKER, '<div class="link_url" style="margin-top: 1em;">' . $track_element . '</div>' . self :: DESCRIPTION_MARKER, $html);
    }

    function get_short_html()
    {
        $object = $this->get_content_object();
        return '<span class="content_object"><a target="about:blank" href="' . htmlentities($object->get_url()) . '">' . htmlentities($object->get_title()) . '</a></span>';
    }

    function get_thumbnail()
    {
        return $this->get_track_element('80%');
    }

    function get_preview($is_thumbnail = false)
    {
        if ($is_thumbnail)
        {
            return $this->get_track_element('80%');
        }
        else
        {
            return $this->get_track_element();
        }
    }
}
?>