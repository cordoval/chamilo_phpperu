<?php
namespace application\personal_calendar;

use HTML_Table;

use common\libraries\YearCalendar;
use common\libraries\Theme;
use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * @package application.personal_calendar.renderer
 *
 * This personal calendar renderer provides a tabular month view to navigate in
 * the calendar
 */
class PersonalCalendarYearRenderer extends PersonalCalendarRenderer
{

    /**
     * @see PersonalCalendarRenderer::render()
     */
    public function render()
    {
        $calendar = new YearCalendar($this->get_time());

        $start_time = $calendar->get_start_time();
//        dump(date('r', $start_time));
        $end_time = $calendar->get_end_time();
//        dump(date('r', $end_time));

        $events = $this->get_events($start_time, $end_time);

        $table_date = $start_time;

        while ($table_date <= $end_time)
        {
            $next_table_date = strtotime('+1 Day', $table_date);

            foreach ($events as $index => $event)
            {
                if (! $calendar->contains_events_for_time($table_date))
                {
                    $start_date = $event->get_start_date();
                    $end_date = $event->get_end_date();

                    if ($table_date < $start_date && $start_date < $next_table_date || $table_date <= $end_date && $end_date <= $next_table_date || $start_date <= $table_date && $next_table_date <= $end_date)
                    {
                        $content = $this->render_event($event);
                        $calendar->add_event($table_date, $content);
                    }
                }
            }
            $table_date = $next_table_date;
        }

        $parameters['time'] = '-TIME-';
        $calendar->add_calendar_navigation($this->get_parent()->get_url($parameters));

        return $calendar->render();
    }

    //    function render_mini_month($time)
    //    {
    //        $calendar = new MiniMonthCalendar($time);
    //
    //        $html = array();
    //
    //        $start_time = $calendar->get_start_time();
    //        $end_time = $calendar->get_end_time();
    //
    //        $events = $this->get_events($start_time, $end_time);
    //
    //        $table_date = $start_time;
    //
    //        while ($table_date <= $end_time)
    //        {
    //            $next_table_date = strtotime('+1 Day', $table_date);
    //
    //            foreach ($events as $index => $event)
    //            {
    //                if (! $calendar->contains_events_for_time($table_date))
    //                {
    //                    $start_date = $event->get_start_date();
    //                    $end_date = $event->get_end_date();
    //
    //                    if ($table_date < $start_date && $start_date < $next_table_date || $table_date <= $end_date && $end_date <= $next_table_date || $start_date <= $table_date && $next_table_date <= $end_date)
    //                    {
    //                        $content = $this->render_event($event);
    //                        $calendar->add_event($table_date, $content);
    //                    }
    //                }
    //            }
    //            $table_date = $next_table_date;
    //        }
    //
    //        $parameters['time'] = '-TIME-';
    //        $calendar->set_navigation_html($this->get_navigation_html($time));
    //        //$calendar->add_calendar_navigation($this->get_parent()->get_url($parameters));
    //        switch ($this->get_parent()->get_parameter('view'))
    //        {
    //            case 'month' :
    //                $calendar->mark_period(MiniMonthCalendar :: PERIOD_MONTH);
    //                break;
    //            case 'week' :
    //                $calendar->mark_period(MiniMonthCalendar :: PERIOD_WEEK);
    //                break;
    //            case 'day' :
    //                $calendar->mark_period(MiniMonthCalendar :: PERIOD_DAY);
    //                break;
    //        }
    //        $calendar->add_navigation_links($this->get_parent()->get_url($parameters));
    //        $html[] = $calendar->render();
    //        return implode("\n", $html);
    //    }


    /**
     * Gets a html representation of a published calendar event
     * @param PersonalCalendarEvent $event
     * @return string
     */
    private function render_event($event)
    {
        $html[] = '<a href="' . $event->get_url() . '"><br /><img src="' . Theme :: get_common_image_path() . 'action_posticon.png"/></a>';
        return implode("\n", $html);
    }
}
?>