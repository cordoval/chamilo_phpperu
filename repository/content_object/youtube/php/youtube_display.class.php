<?php
namespace repository\content_object\youtube;

use common\libraries\Text;

use repository\ContentObjectDisplay;

/**
 * $Id: youtube_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.youtube
 */
class YoutubeDisplay extends ContentObjectDisplay
{

    function get_video_element($width = 425, $height = 344)
    {
        $object = $this->get_content_object();
        $video_url = $object->get_video_url();

        return '<embed style="margin-bottom: 1em;" height="' . $height . '" width="' . $width . '" type="application/x-shockwave-flash" src="' . $object->get_video_url() . '"></embed>';
    }

    function get_description()
    {
        $html = parent :: get_description();
        $object = $this->get_content_object();
        $video_element = $this->get_video_element();

        return str_replace(self :: DESCRIPTION_MARKER, '<div class="link_url" style="margin-top: 1em;">' . $video_element . '<br/><a href="' . htmlentities($object->get_video_url()) . '">' . htmlentities($object->get_video_url()) . '</a></div>' . self :: DESCRIPTION_MARKER, $html);
    }

    function get_short_html()
    {
        $object = $this->get_content_object();
        return '<span class="content_object"><a target="about:blank" href="' . htmlentities($object->get_video_url()) . '">' . htmlentities($object->get_title()) . '</a></span>';
    }

    function get_thumbnail()
    {
        return $this->get_video_element(280, 226);
    }

    function get_preview($is_thumbnail = false)
    {
        if ($is_thumbnail)
        {
            return $this->get_video_element(280, 226);
        }
        else
        {
            return $this->get_video_element();
        }
    }
}
?>