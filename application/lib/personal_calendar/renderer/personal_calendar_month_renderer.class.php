<?php

/**
 * $Id: personal_calendar_month_renderer.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.personal_calendar.renderer
 */
require_once (dirname(__FILE__) . '/../personal_calendar_renderer.class.php');
/**
 * This personal calendar renderer provides a tabular month view of the events
 * in the calendar.
 */
class PersonalCalendarMonthRenderer extends PersonalCalendarRenderer
{

    /**
     * @see PersonalCalendarRenderer::render()
     */
    public function render()
    {
        $calendar = new MonthCalendar($this->get_time());
        
        $html = array();
        
        $start_time = $calendar->get_start_time();
        $end_time = $calendar->get_end_time();
        
        $events = $this->get_events($start_time, $end_time);
        
        $table_date = $start_time;
        
        while ($table_date <= $end_time)
        {
            $next_table_date = strtotime('+1 Day', $table_date);
            
            foreach ($events as $index => $event)
            {
                
                $start_date = $event->get_start_date();
                $end_date = $event->get_end_date();
                
                if ($table_date < $start_date && $start_date < $next_table_date || $table_date <= $end_date && $end_date <= $next_table_date || $start_date <= $table_date && $next_table_date <= $end_date)
                {
                    $content = $this->render_event($event, $table_date);
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
    private function render_event($event, $table_date)
    {
        $start_date = $event->get_start_date();
        $end_date = $event->get_end_date();
        
        $from_date = strtotime(date('Y-m-1', $this->get_time()));
        $to_date = strtotime('-1 Second', strtotime('Next Month', $from_date));
        
        $html[] = '<div class="event' . (($start_date < $from_date || $start_date > $to_date) ? ' event_fade' : '') . '" style="border-left: 5px solid ' . $this->get_color(Translation :: get(Application :: application_to_class($event->get_source())), (($start_date < $from_date || $start_date > $to_date) ? true : false)) . ';">';
        
        if ($start_date > $table_date && $start_date <= strtotime('+1 Day', $table_date))
        {
            $html[] = date('H:i', $start_date);
        }
        else
        {
            $html[] = '&rarr;';
        }
        
        $html[] = '<a href="' . $event->get_url() . '">';
        $html[] = htmlspecialchars($event->get_title());
        $html[] = '</a>';
        
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