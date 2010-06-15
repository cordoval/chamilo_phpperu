<?php
/**
 * $Id: month_calendar_content_object_publication_list_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.browser.list_renderer
 */
require_once dirname(__FILE__) . '/../content_object_publication_list_renderer.class.php';
/**
 * Renderer to display events in a month calendar
 */
class MonthCalendarContentObjectPublicationListRenderer extends ContentObjectPublicationListRenderer
{
    /**
     * The current time displayed in the calendar
     */
    private $display_time;

    /**
     * Sets the current display time.
     * @param int $time The current display time.
     */
    function set_display_time($time)
    {
        $this->display_time = $time;
    }

    function get_display_time()
    {
        return $this->display_time;
    }

    /**
     * Returns the HTML output of this renderer.
     * @return string The HTML output
     */
    function as_html()
    {
        $calendar_table = new MonthCalendar($this->display_time);
        $start_time = $calendar_table->get_start_time();
        $end_time = $calendar_table->get_end_time();
        $table_date = $start_time;

        $publications = $this->get_calendar_events($start_time, $end_time);

        while ($table_date <= $end_time)
        {
            $next_table_date = strtotime('+1 Day', $table_date);

            foreach ($publications as $index => $publication)
            {
                $object = $publication->get_content_object();

                $start_date = $object->get_start_date();
                $end_date = $object->get_end_date();

                if ($table_date < $start_date && $start_date < $next_table_date || $table_date <= $end_date && $end_date <= $next_table_date || $start_date <= $table_date && $next_table_date <= $end_date)
                {
                    $cell_contents = $this->render_publication($publication, $table_date);
                    $calendar_table->add_event($table_date, $cell_contents);
                }
            }
            $table_date = $next_table_date;
        }
        $url_format = $this->get_url(array('time' => '-TIME-'));
        $calendar_table->add_calendar_navigation($url_format);
        $html[] = $calendar_table->render();
        return implode("\n", $html);
    }

    function get_calendar_events($from_time, $to_time)
    {
        $publications = $this->get_publications();

        $events = array();
        foreach ($publications as $index => $publication)
        {
            $events[] = $this->get_browser()->get_parent()->convert_content_object_publication_to_calendar_event($publication);

//            $object = $publication->get_content_object();

            //            if ($object->repeats())
            //            {
            //                $repeats = $object->get_repeats($from_time, $to_time);
            //
            //                foreach ($repeats as $repeat)
            //                {
            //                    $the_publication = clone $publication;
            //                    $the_publication->set_content_object($repeat);
            //
            //                    $events[] = $the_publication;
            //                }
            //            }
            //            elseif ($from_time <= $object->get_start_date() && $object->get_start_date() <= $to_time || $from_time <= $object->get_end_date() && $object->get_end_date() <= $to_time || $object->get_start_date() <= $from_time && $to_time <= $object->get_end_date())
            //            {
//            $object_publication = new ContentObjectPublication();
//            $calendar_event = ContentObject :: factory(CalendarEvent :: get_type_name());
//            $calendar_event->set_title($object->get_title());
//            $calendar_event->set_description($object->get_description());
//            $calendar_event->set_start_date(time());
//            $calendar_event->set_end_date(time());
//            $object_publication->set_content_object($calendar_event);
//            $events[] = $object_publication;
            //            }
        }

        return $events;
    }

    /**
     * Renders a publication
     * @param ContentObjectPublication $publication The publication to render
     * @param int $table_date The current date displayed in the table.
     */
    function render_publication($publication, $table_date)
    {
        static $color_cache;
        $event = $publication->get_content_object();
        $event_url = $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_VIEW, Tool :: PARAM_PUBLICATION_ID => $publication->get_id()), array(), true);
        $start_date = $event->get_start_date();
        $end_date = $event->get_end_date();
        if (! isset($color_cache[$event->get_id()]))
        {
            $rgb = $this->object2color($event);
            $color_cache[$event->get_id()]['full'] = 'rgb(' . $rgb['r'] . ',' . $rgb['g'] . ',' . $rgb['b'] . ')';
            $color_cache[$event->get_id()]['fade'] = 'rgb(' . $rgb['fr'] . ',' . $rgb['fg'] . ',' . $rgb['fb'] . ')';
        }
        $html[] = '';

        $from_date = strtotime(date('Y-m-1', $this->get_display_time()));
        //		echo date('r', $from_date);
        $to_date = strtotime('-1 Second', strtotime('Next Month', $this->get_display_time()));

        $html[] = '<div class="event' . ($start_date < $from_date || $start_date > $to_date ? ' event_fade' : '') . '" style="border-right: 4px solid ' . $color_cache[$event->get_id()][($start_date < $from_date || $start_date > $to_date ? 'fade' : 'full')] . ';">';
        if ($start_date > $table_date && $start_date <= strtotime('+1 Day', $table_date))
        {
            $html[] = date('H:i', $start_date);
        }
        else
        {
            $html[] = '&rarr;';
        }
        $html[] = '<a href="' . $event_url . '">' . htmlspecialchars($event->get_title()) . '</a>';
        if ($start_date != $end_date && $end_date > strtotime('+1 Day', $start_date))
        {
            if ($end_date >= $table_date && $end_date < strtotime('+1 Day', $table_date))
            {
                $html[] = date('H:i', $end_date);
            }
            else
            {
                $html[] = '&rarr;';
            }
        }
        $html[] = '</div>';
        return implode("\n", $html);
    }
}
?>