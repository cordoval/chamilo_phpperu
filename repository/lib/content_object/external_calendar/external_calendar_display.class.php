<?php
/**
 * $Id: external_calendar_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.link
 */

class ExternalCalendarDisplay extends ContentObjectDisplay
{
    function get_full_html()
    {
        $object = $this->get_content_object();
        
        $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/' . $object->get_icon_name() . ($object->is_latest_version() ? '' : '_na') . '.png);">';
        $html[] = '<div class="title">' . Translation :: get('Description') . '</div>';
        $html[] = $this->get_description();
        $html[] = '<div class="link_url" style="margin-top: 1em;"><a href="' . htmlentities($object->get_url()) . '">' . htmlentities($object->get_url()) . '</a></div>';
        
        $number_of_events = $object->count_events();
		$html[] = Translation::get('EventCount') . ' : ' . $number_of_events; 
        $html[] = '</div>';
        return implode("\n", $html);
    }

    function get_short_html()
    {
        $object = $this->get_content_object();
        return '<span class="content_object"><a target="about:blank" href="' . htmlentities($object->get_url()) . '">' . htmlentities($object->get_title()) . '</a></span>';
    }
}
?>