<?php
/**
 * $Id: month_calendar_content_object_publication_list_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.browser.list_renderer
 */
require_once dirname(__FILE__) . '/calendar_content_object_publication_list_renderer.class.php';
/**
 * Renderer to display events in a month calendar
 */
class MonthCalendarContentObjectPublicationListRenderer extends CalendarContentObjectPublicationListRenderer
{

    /**
     * Returns the HTML output of this renderer.
     * @return string The HTML output
     */
    function as_html()
    {
        $calendar_table = new MonthCalendar($this->get_display_time());
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