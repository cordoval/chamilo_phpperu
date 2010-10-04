<?php
/**
 * $Id: vimeo_display.class.php 2010-06-08
 * package repository.lib.content_object.vimeo
 * @author Shoira Mukhsinova
 */
class VimeoDisplay extends ContentObjectDisplay
{

    function get_description()
    {
        $html = parent :: get_description();
        $object = $this->get_content_object();

        $video_url = $object->get_url();
        $url = $object->get_video_url();

        $video_element = '<embed style="margin-bottom: 1em;" type="application/x-shockwave-flash" height="' . $object->get_height() . '" width="' . $object->get_width() . '"src="' . $url . '"></embed>';

        return str_replace(self :: DESCRIPTION_MARKER, '<div class="link_url" style="margin-top: 1em;">' . $video_element . '<br/><a href="' . htmlentities($object->get_url()) . '">' . htmlentities($object->get_url()) . '</a></div>' . self :: DESCRIPTION_MARKER, $html);
    }

    function get_short_html()
    {
        $object = $this->get_content_object();
        return '<span class="content_object"><a target="about:blank" href="' . htmlentities($object->get_url()) . '">' . htmlentities($object->get_title()) . '</a></span>';
    }
}
?>