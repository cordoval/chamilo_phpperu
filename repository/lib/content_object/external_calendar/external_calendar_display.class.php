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
        $event_id = Request:: get(ExternalCalendar::PARAM_EVENT_ID);
        if (isset($event_id))
        {
        	$event = $object->get_event($event_id);
        	$ical_recurrence = new IcalRecurrence($event);
        	$date_format = Translation :: get('dateTimeFormatLong');
        	$html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/' . $object->get_icon_name() . ($object->is_latest_version() ? '' : '_na') . '.png);">';
        	$html[] = '<div class="title">' . $event->summary['value'] . '</div>';
        	$html[] = '<div class="calendar_event_range" style="font-weight: bold;">';
            $html[] = Translation :: get('From');
            $html[] = ' ';
            $html[] = DatetimeUtilities :: convert_time_to_timezone($ical_recurrence->get_start_date(), $date_format);
            $html[] = ' ';
            $html[] = Translation :: get('Until');
            $html[] = ' ';
            $html[] = DatetimeUtilities :: convert_time_to_timezone($ical_recurrence->get_end_date(), $date_format);
            $html[] = '</div>';
            if ($ical_recurrence->repeats())
            {
            	$html[] = $ical_recurrence->get_repeat() . '<br/>';
            }
            $html[] =  $event->description[0]['value'];
            $html[] = '</div>';
        }
        else 
        {
        	$html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/' . $object->get_icon_name() . ($object->is_latest_version() ? '' : '_na') . '.png);">';
	        $html[] = '<div class="title">' . Translation :: get('Description') . '</div>';
	        $html[] = $this->get_description();
	        $html[] = '<div class="link_url" style="margin-top: 1em;"><a href="' . htmlentities($object->get_url()) . '">' . htmlentities($object->get_url()) . '</a></div>';
	        
	        $number_of_events = $object->count_events();
			$html[] = Translation::get('EventCount') . ' : ' . $number_of_events; 
	        $html[] = '</div>';
        }
        
        return implode("\n", $html);
    }

    function get_short_html()
    {
        $object = $this->get_content_object();
        return '<span class="content_object"><a target="about:blank" href="' . htmlentities($object->get_url()) . '">' . htmlentities($object->get_title()) . '</a></span>';
    }
}
?>