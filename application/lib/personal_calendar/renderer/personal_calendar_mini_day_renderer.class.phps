<?php
/**
 * $Id: personal_calendar_mini_day_renderer.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.personal_calendar.renderer
 */
require_once (dirname(__FILE__) . '/../personal_calendar_renderer.class.php');
/**
 * This personal calendar renderer provides a tabular day view of the events in
 * the calendar.
 */
class PersonalCalendarMiniDayRenderer extends PersonalCalendarRenderer
{
    private $hour_step;
    private $start_hour;
    private $end_hour;

    public function PersonalCalendarMiniDayRenderer($personal_calendar, $display_time, $hour_step = '1', $start_hour = '0', $end_hour = '24')
    {
        $this->hour_step = $hour_step;
        $this->start_hour = $start_hour;
        $this->end_hour = $end_hour;
        parent :: __construct($personal_calendar, $display_time);
    }

    /**
     * @see PersonalCalendarRenderer::render()
     */
    public function render()
    {
        $calendar = new MiniDayCalendar($this->get_time(), $this->hour_step, $this->start_hour, $this->end_hour);
        $from_date = $calendar->get_start_time();
        $to_date = $calendar->get_end_time();
        $events = $this->get_events($from_date, $to_date);
        
        $html = array();
        
        $start_time = $calendar->get_start_time();
        $end_time = $calendar->get_end_time();
        $table_date = $start_time;
        
        while ($table_date <= $end_time)
        {
            $next_table_date = strtotime('+' . $calendar->get_hour_step() . ' Hours', $table_date);
            
            foreach ($events as $index => $event)
            {
                $start_date = $event->get_start_date();
                $end_date = $event->get_end_date();
                
                if ($table_date < $start_date && $start_date < $next_table_date || $table_date < $end_date && $end_date < $next_table_date || $start_date <= $table_date && $next_table_date <= $end_date)
                {
                    $content = $this->render_event($event, $table_date, $calendar->get_hour_step());
                    $calendar->add_event($table_date, $content);
                }
            }
            $table_date = $next_table_date;
        }
        
        $parameters['time'] = '-TIME-';
        $calendar->add_calendar_navigation($this->get_parent()->get_url($parameters));
        $html[] = $calendar->render();
        $html[] = $this->build_legend();
        return implode("\n", $html);
    }

    /**
     * Gets a html representation of a calendar event
     * @param PersonalCalendarEvent $event
     * @return string
     */
    private function render_event($event, $table_start_date, $calendar_hour_step)
    {
        $table_end_date = strtotime('+' . $calendar_hour_step . ' Hours', $table_start_date);
        $start_date = $event->get_start_date();
        $end_date = $event->get_end_date();
        
        $html[] = '<div class="event" style="border-left: 5px solid ' . $this->get_color(Translation :: get(Application :: application_to_class($event->get_source()))) . ';">';
        
        if ($start_date >= $table_start_date && $start_date < $table_end_date)
        {
            $html[] = date('H:i', $start_date);
        }
        else
        {
            $html[] = '&darr;';
        }
        
        $html[] = '<a href="' . $event->get_url() . '">';
        $html[] = htmlspecialchars($event->get_title());
        $html[] = '</a>';
        
        if ($start_date != $end_date && $end_date > strtotime('+' . $calendar_hour_step . ' hours', $start_date))
        {
            if ($end_date > $table_start_date && $end_date <= $table_end_date)
            {
                $html[] = date('H:i', $end_date);
            }
            else
            {
                $html[] = '&darr;';
            }
        }
        
        $html[] = '</div>';
        return implode("\n", $html);
    }
}
?>