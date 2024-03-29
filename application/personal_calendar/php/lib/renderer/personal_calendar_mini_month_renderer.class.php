<?php

namespace application\personal_calendar;

use common\libraries\MiniMonthCalendar;
use common\libraries\Theme;

/**
 * @package application.personal_calendar.renderer
 *
 * A tabular mini month view to navigate in the calendar
 */
class PersonalCalendarMiniMonthRenderer extends PersonalCalendarRenderer {

    public function render() {
        $calendar = new MiniMonthCalendar($this->get_time());

        $html = array();

        $start_time = $calendar->get_start_time();
        $end_time = $calendar->get_end_time();

        $events = $this->get_events($start_time, $end_time);

        $table_date = $start_time;

        while ($table_date <= $end_time) {
            $next_table_date = strtotime('+1 Day', $table_date);

            foreach ($events as $index => $event) {
                $start_date = $event->get_start_date();
                $end_date = $event->get_end_date();

                if ($table_date < $start_date && $start_date < $next_table_date || $table_date <= $end_date && $end_date <= $next_table_date || $start_date <= $table_date && $next_table_date <= $end_date) {
                    if (!$calendar->contains_events_for_time($table_date)) {
                        $marker = '<br /><div class="event_marker" style="width: 14px; height: 15px;"><img src="' . htmlspecialchars(Theme :: get_common_image_path()) . 'action_marker.png"/></div>';
                        $calendar->add_event($table_date, $marker);
                    }

                    $content = $this->render_event($event, $table_date);
                    $calendar->add_event($table_date, $content);
                }
            }
            $table_date = $next_table_date;
        }

        $parameters['time'] = '-TIME-';
        $calendar->add_calendar_navigation($this->get_parent()->get_url($parameters));
        switch ($this->get_parent()->get_parameter('view')) {
            case 'month' :
                $calendar->mark_period(MiniMonthCalendar :: PERIOD_MONTH);
                break;
            case 'week' :
                $calendar->mark_period(MiniMonthCalendar :: PERIOD_WEEK);
                break;
            case 'day' :
                $calendar->mark_period(MiniMonthCalendar :: PERIOD_DAY);
                break;
        }
        $calendar->add_navigation_links($this->get_parent()->get_url($parameters));
        $html[] = $calendar->render();
        return implode("\n", $html);
    }

    //    /**
    //     * Gets a html representation of a published calendar event
    //     * @param PersonalCalendarEvent $event
    //     * @return string
    //     */
    //    private function render_event($event)
    //    {
    //        $html[] = '<a href="' . $event->get_url() . '"><br /><img src="' . htmlspecialchars(Theme :: get_common_image_path()) . 'action_marker.png"/></a>';
    //        return implode("\n", $html);
    //    }
    /**
     * Gets a html representation of a calendar event
     * @param PersonalCalendarEvent $event
     * @return string
     */
    private function render_event($event, $table_date) {
        $start_date = $event->get_start_date();
        $end_date = $event->get_end_date();

        $from_date = strtotime(date('Y-m-1', $this->get_time()));
        $to_date = strtotime('-1 Second', strtotime('Next Month', $from_date));

        $html[] = '<div class="event" style="display: none; border-left: 5px solid ' . $this->get_color($event->get_source()) . ';">';

        if ($start_date > $table_date && $start_date <= strtotime('+1 Day', $table_date)) {
            $html[] = date('H:i', $start_date);
        } else {
            $html[] = '&rarr;';
        }

        $target = $this->get_link_target();
        $target = $target ? ' target="' . $target . '" ' : '';

        $html[] = '<a href="' . htmlspecialchars($event->get_url()) . '"'.$target.'>';
        $html[] = htmlspecialchars($event->get_title());
        $html[] = '</a>';

        if ($start_date != $end_date && $end_date > strtotime('+1 Day', $start_date)) {
            if ($end_date >= $table_date && $end_date < strtotime('+1 Day', $table_date)) {
                $html[] = date('H:i', $end_date);
            } else {
                $html[] = '&rarr;';
            }
        }

        $html[] = '</div>';
        return implode("\n", $html);
    }

}

?>