<?php
/**
 * $Id: reservations_calendar_mini_month_renderer.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.calendar
 */
require_once (dirname(__FILE__) . '/reservations_calendar_renderer.class.php');
/**
 * This personal calendar renderer provides a tabular month view to navigate in
 * the calendar
 */
class ReservationsCalendarMiniMonthRenderer extends ReservationsCalendarRenderer
{

    /**
     * @see ReservationsCalendarRenderer::render()
     */
    public function render()
    {
        $calendar = new MiniMonthCalendar($this->get_time());
        $from_date = strtotime(date('Y-m-1', $this->get_time()));
        $to_date = strtotime('-1 Second', strtotime('Next Month', $from_date));
        $db_from = Utilities :: to_db_date($from_date);
        $db_to = Utilities :: to_db_date($to_date);
        
        $rdm = ReservationsDataManager :: get_instance();
        
        $conditions[] = $rdm->get_reservations_condition($db_from, $db_to, $_GET['item_id']);
        $conditions[] = new EqualityCondition(Reservation :: PROPERTY_STATUS, Reservation :: STATUS_NORMAL);
        $condition = new AndCondition($conditions);
        
        $reservations = $rdm->retrieve_reservations($condition);
        while ($reservation = $reservations->next_result())
            $res[] = $reservation;
        
        $html = array();
        
        $start_time = $calendar->get_start_time();
        $end_time = $calendar->get_end_time();
        $table_date = $start_time;
        
        while ($table_date <= $end_time)
        {
            $next_table_date = strtotime('+24 Hours', $table_date);
            
            foreach ($res as $index => $reservation)
            {
                if (! $calendar->contains_events_for_time($table_date))
                {
                    $start_date = Utilities :: time_from_datepicker($reservation->get_start_date());
                    $end_date = Utilities :: time_from_datepicker($reservation->get_stop_date());
                    if ($table_date < $start_date && $start_date < $next_table_date || $table_date <= $end_date && $end_date <= $next_table_date || $start_date <= $table_date && $next_table_date <= $end_date)
                    {
                        
                        $content = $this->render_reservation($reservation);
                        $calendar->add_event($table_date, $content);
                    }
                }
            }
            $table_date = $next_table_date;
        }
        
        $parameters['time'] = '-TIME-';
        $parameters['item_id'] = $_GET['item_id'];
        $calendar->add_calendar_navigation($this->get_parent()->get_url($parameters));
        $calendar->mark_period(MiniMonthCalendar :: PERIOD_WEEK);
        $calendar->add_navigation_links($this->get_parent()->get_url($parameters));
        $html = $calendar->render();
        return $html;
    }

    /**
     * Gets a html representation of a published calendar event
     * @param ReservationsCalendarEvent $event
     * @return string
     */
    private function render_reservation($reservation)
    {
        $html[] = '<br /><img src="' . Theme :: get_common_image_path() . 'action_posticon.png"/>';
        return implode("\n", $html);
    }
}
?>