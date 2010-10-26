<?php
namespace application\weblcms;

use common\libraries\Theme;
use common\libraries\Request;
use common\libraries\MiniMonthCalendar;

/**
 * $Id: mini_month_calendar_content_object_publication_list_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.browser.list_renderer
 */
require_once dirname(__FILE__) . '/calendar_content_object_publication_list_renderer.class.php';
/**
 * Renderer to display events in a month calendar
 */
class MiniMonthCalendarContentObjectPublicationListRenderer extends CalendarContentObjectPublicationListRenderer
{

    /**
     * Returns the HTML output of this renderer.
     * @return string The HTML output
     */
    function as_html()
    {
        $calendar_table = new MiniMonthCalendar($this->get_display_time());
        $start_time = $calendar_table->get_start_time();
        $end_time = $calendar_table->get_end_time();

        $publications = $this->get_calendar_events($start_time, $end_time);

        $table_date = $start_time;
        while ($table_date <= $end_time)
        {
            $next_table_date = strtotime('+24 Hours', $table_date);

            foreach ($publications as $index => $publication)
            {
                if (! $calendar_table->contains_events_for_time($table_date))
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
            }

            $table_date = $next_table_date;
        }
        $url_format = $this->get_url(array('time' => '-TIME-', 'view' => Request :: get('view')));
        $calendar_table->add_calendar_navigation($url_format);
        switch ($this->get_view())
        {
            case self :: CALENDAR_MONTH_VIEW :
                $calendar_table->mark_period(MiniMonthCalendar :: PERIOD_MONTH);
                break;
            case self :: CALENDAR_WEEK_VIEW :
                $calendar_table->mark_period(MiniMonthCalendar :: PERIOD_WEEK);
                break;
            case self :: CALENDAR_DAY_VIEW :
                $calendar_table->mark_period(MiniMonthCalendar :: PERIOD_DAY);
                break;
        }
        $calendar_table->add_navigation_links($url_format);
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
        $event = $publication->get_content_object();
        $start_date = $event->get_start_date();
        $end_date = $event->get_end_date();
        $html[] = '<br /><img src="' . Theme :: get_common_image_path() . 'action_posticon.png"/>';
        return implode("\n", $html);
    }
}
?>