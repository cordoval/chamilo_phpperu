<?php
namespace repository\content_object\calendar_event;

use common\libraries\Translation;
use common\libraries\DatetimeUtilities;

use repository\ContentObjectDisplay;

/**
 * $Id: calendar_event_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.calendar_event
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
/**
 * This class can be used to display calendar events
 */
class CalendarEventDisplay extends ContentObjectDisplay
{

    // Inherited
    function get_full_html()
    {
        return parent :: get_full_html();
    }

    function get_description()
    {
        $description = parent :: get_description();
        $object = $this->get_content_object();
        $date_format = Translation :: get('dateTimeFormatLong');

        $prepend = array();

        $repeats = $object->repeats();

        if ($repeats)
        {
            $prepend[] = '<div class="calendar_event_range" style="font-weight: bold;">';
            $prepend[] = Translation :: get('Repeats');
            $prepend[] = ' ';
            $prepend[] = strtolower($object->get_repeat_as_string());
            $prepend[] = ' ';
            $prepend[] = Translation :: get('Until');
            $prepend[] = ' ';
            $prepend[] = DatetimeUtilities :: format_locale_date($date_format, $object->get_repeat_to());
            $prepend[] = '</div>';
        }
        else
        {
            $prepend[] = '<div class="calendar_event_range" style="font-weight: bold;">';
            $prepend[] = Translation :: get('From');
            $prepend[] = ' ';
            //$prepend[] = DatetimeUtilities :: format_locale_date($date_format, $object->get_start_date());
            $prepend[] = DatetimeUtilities :: format_locale_date($date_format, $object->get_start_date());
            $prepend[] = ' ';
            $prepend[] = Translation :: get('Until');
            $prepend[] = ' ';
            //$prepend[] = DatetimeUtilities :: format_locale_date($date_format, $object->get_end_date());
            $prepend[] = DatetimeUtilities :: format_locale_date($date_format, $object->get_end_date());
            $prepend[] = '</div>';
        }

        return implode('', $prepend) . $description;
    }
}
?>