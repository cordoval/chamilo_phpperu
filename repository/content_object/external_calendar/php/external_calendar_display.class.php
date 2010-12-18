<?php
namespace repository\content_object\external_calendar;

use repository\ContentObject;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Theme;
use common\libraries\DatetimeUtilities;

use repository\ContentObjectDisplay;

use IcalRecurrence;

/**
 * $Id: external_calendar_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.link
 */

class ExternalCalendarDisplay extends ContentObjectDisplay
{

    function get_full_html()
    {
        $object = $this->get_content_object();
        $event_id = Request :: get(ExternalCalendar :: PARAM_EVENT_ID);
        if (isset($event_id))
        {
            $event = $object->get_event($event_id);
            $ical_recurrence = new IcalRecurrence($event);
            $date_format = Translation :: get('DateTimeFormatLong', null , Utilities :: COMMON_LIBRARIES);
            $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_image_path(ContentObject :: get_content_object_type_namespace($object->get_type())) . 'logo/' . $object->get_icon_name() . ($object->is_latest_version() ? '' : '_na') . '.png);">';
            $html[] = '<div class="title">' . $event->summary['value'] . '</div>';
            $html[] = '<div class="calendar_event_range" style="font-weight: bold;">';
            $html[] = Translation :: get('From', null , Utilities :: COMMON_LIBRARIES);
            $html[] = ' ';
            $html[] = DatetimeUtilities :: format_locale_date($date_format, $ical_recurrence->get_start_date());
            $html[] = ' ';
            $html[] = Translation :: get('Until', null , Utilities :: COMMON_LIBRARIES);
            $html[] = ' ';
            $html[] = DatetimeUtilities :: format_locale_date($date_format, $ical_recurrence->get_end_date());
            $html[] = '</div>';
            if ($ical_recurrence->repeats())
            {
                $html[] = $ical_recurrence->get_repeat() . '<br/>';
            }
            $html[] = $event->description[0]['value'];
            $html[] = '</div>';
        }
        else
        {
            $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_image_path(ContentObject :: get_content_object_type_namespace($object->get_type())) . 'logo/' . $object->get_icon_name() . ($object->is_latest_version() ? '' : '_na') . '.png);">';
            $html[] = '<div class="title">' . Translation :: get('Description', null , Utilities :: COMMON_LIBRARIES) . '</div>';
            $html[] = $this->get_description();
            $html[] = '<div class="link_url" style="margin-top: 1em;"><a href="' . htmlentities($object->get_url()) . '">' . htmlentities($object->get_url()) . '</a></div>';

            $number_of_events = $object->count_events();
            $html[] = Translation :: get('EventCount') . ' : ' . $number_of_events;
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