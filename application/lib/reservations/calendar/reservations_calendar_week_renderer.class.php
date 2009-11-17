<?php
/**
 * $Id: reservations_calendar_week_renderer.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.calendar
 */
require_once (dirname(__FILE__) . '/reservations_calendar_renderer.class.php');
/**
 * This personal calendar renderer provides a tabular week view of the events in
 * the calendar.
 */
class ReservationsCalendarWeekRenderer extends ReservationsCalendarRenderer
{

    /**
     * @see PersonalCalendarRenderer::render()
     */
    public function render($item_id = null)
    {
        if (! $item_id)
            $item_id = Request :: get('item_id');
        $calendar = new MiniWeekCalendar($this->get_time(), 2);
        $from_date = strtotime('Last Monday', strtotime('+1 Day', strtotime(date('Y-m-d', $this->get_time()))));
        $to_date = strtotime('-1 Second', strtotime('Next Week', $from_date));
        $now = time();
        $html = array();
        
        $base = $this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_CREATE_SUBSCRIPTION));
        
        $db_from = Utilities :: to_db_date($from_date);
        $db_to = Utilities :: to_db_date($to_date);
        
        $rdm = ReservationsDataManager :: get_instance();
        $item = $rdm->retrieve_items(new EqualityCondition(Item :: PROPERTY_ID, $item_id))->next_result();
        
        if ($item->get_blackout() == 1)
        {
            $times[] = array('start_date' => date("Y-m-d H:i", $from_date), 'stop_date' => date("Y-m-d H:i", $to_date), 'type' => 'Blackout');
        }
        else
        {
            $bool = true;
            
            if (! $this->get_parent()->has_right('item', $item->get_id(), ReservationsRights :: MAKE_RESERVATION_RIGHT))
                $bool = false;
            
            $conditions[] = $rdm->get_reservations_condition($db_from, $db_to, $item_id);
            $conditions[] = new EqualityCondition(Reservation :: PROPERTY_STATUS, Reservation :: STATUS_NORMAL);
            $condition = new AndCondition($conditions);
            
            $reservations = $rdm->retrieve_reservations($condition);
            //$reservations = $rdm->retrieve_reservations($rdm->get_reservations_condition($db_from, $db_to, $item_id));
            while ($reservation = $reservations->next_result())
            {
                $end_time = Utilities :: time_from_datepicker($reservation->get_stop_date());
                $url = $base . '&reservation_id=' . $reservation->get_id();
                
                if ($now > $end_time)
                {
                    $times[] = array('start_date' => $reservation->get_start_date(), 'stop_date' => $reservation->get_stop_date(), 'type' => 'Outofperiod');
                }
                else
                {
                    $conditions = array();
                    $conditions[] = new EqualityCondition(Subscription :: PROPERTY_RESERVATION_ID, $reservation->get_id());
                    $conditions[] = new EqualityCondition(Subscription :: PROPERTY_STATUS, Subscription :: STATUS_NORMAL);
                    $condition = new AndCondition($conditions);
                    
                    $subscriptions = $rdm->retrieve_subscriptions($condition);
                    if ($reservation->get_type() != Reservation :: TYPE_TIMEPICKER)
                    {
                        if ($subscriptions->size() == 0 || $subscriptions->size() < $reservation->get_max_users())
                        {
                            $times[] = array('start_date' => $reservation->get_start_date(), 'stop_date' => $reservation->get_stop_date(), 'type' => 'OpenReservation', 'url' => $bool ? $url : '');
                        }
                        else
                        {
                            $times[] = array('start_date' => $reservation->get_start_date(), 'stop_date' => $reservation->get_stop_date(), 'type' => 'Reserved');
                        }
                    
                    }
                    else
                    {
                        
                        if ($subscriptions->size() == 0)
                        {
                            $times[] = array('start_date' => $reservation->get_start_date(), 'stop_date' => $reservation->get_stop_date(), 'type' => 'Timepicker', 'url' => $bool ? $url : '');
                        }
                        else
                        {
                            $subs = array();
                            
                            while ($subscription = $subscriptions->next_result())
                            {
                                $subs[$subscription->get_start_time()] = $subscription->get_stop_time();
                            }
                            
                            ksort($subs, SORT_STRING);
                            
                            $previous_stop = $reservation->get_start_date();
                            
                            foreach ($subs as $start => $stop)
                            {
                                $previous_stop_time = Utilities :: time_from_datepicker($previous_stop);
                                $start_time = Utilities :: time_from_datepicker($start);
                                //							$stop_time = Utilities :: time_from_datepicker($stop);
                                

                                if (($difference = ($start_time - $previous_stop_time)) > 0)
                                {
                                    if ($difference > ($reservation->get_timepicker_min() * 60))
                                    {
                                        $times[] = array('start_date' => $previous_stop, 'stop_date' => $start, 'type' => 'Timepicker', 'url' => $bool ? $url : '');
                                    }
                                    else
                                    {
                                        $times[] = array('start_date' => $previous_stop, 'stop_date' => $start, 'type' => 'TimepickerToSmall');
                                    }
                                }
                                
                                $times[] = array('start_date' => $start, 'stop_date' => $stop, 'type' => 'Reserved');
                                
                                $previous_stop = $stop;
                            
                            }
                            
                            $previous_stop_time = Utilities :: time_from_datepicker($previous_stop);
                            if (($difference = ($end_time - $previous_stop_time)) > 0)
                            {
                                if ($difference > ($reservation->get_timepicker_min() * 60))
                                {
                                    $times[] = array('start_date' => $previous_stop, 'stop_date' => $reservation->get_stop_date(), 'type' => 'Timepicker', 'url' => $bool ? $url : '');
                                }
                                else
                                {
                                    $times[] = array('start_date' => $previous_stop, 'stop_date' => $reservation->get_stop_date(), 'type' => 'TimepickerToSmall');
                                }
                            
                            }
                        }
                    }
                }
            }
        }
        
        $html = array();
        
        $start_time = $calendar->get_start_time();
        $end_time = $calendar->get_end_time();
        $table_date = $start_time;
        
        while ($table_date <= $to_date)
        {
            $next_table_date = strtotime('+' . $calendar->get_hour_step() . ' Hours', $table_date);
            $blocks = array();
            foreach ($times as $time)
            {
                $start_date = Utilities :: time_from_datepicker($time['start_date']);
                $end_date = Utilities :: time_from_datepicker($time['stop_date']);
                if ($table_date < $start_date && $start_date < $next_table_date || $table_date <= $end_date && $end_date <= $next_table_date || $start_date <= $table_date && $next_table_date <= $end_date)
                {
                    $blocks[] = $time;
                }
            }
            
            $content = $this->render_cell($blocks, $table_date, $calendar->get_hour_step());
            $calendar->add_event($table_date, $content);
            
            $table_date = $next_table_date;
        }
        
        $parameters['time'] = '-TIME-';
        $parameters['item_id'] = $item_id;
        $calendar->add_calendar_navigation($this->get_parent()->get_url($parameters));
        $html = $calendar->toHtml();
        $html .= $this->build_legend();
        return $html;
    }

    private function render_cell($blocks, $table_start_date, $calendar_hour_step)
    {
        $table_end_date = strtotime('+' . $calendar_hour_step . ' hours', $table_start_date);
        
        $prev_stop_date = $table_start_date;
        
        foreach ($blocks as $block)
        {
            $start_date = Utilities :: time_from_datepicker($block['start_date']);
            $end_date = Utilities :: time_from_datepicker($block['stop_date']);
            $difference = $start_date - $prev_stop_date;
            
            $title = date('H:i', $start_date) . '-' . date('H:i', $end_date);
            
            if ($difference > 0)
            {
                $width_block = ($difference / 7200) * 100;
                $html[] = '<div title="' . $title . '" style="float:left; position: relative; width: ' . $width_block . '%; height: 100%;">';
                $html[] = '</div>';
                $prev_stop_date = $start_date;
            }
            
            $ed = ($end_date < $table_end_date) ? $end_date : $table_end_date;
            
            $width = ((($ed - $prev_stop_date) / 7200) * 100);
            
            if (isset($block['url']) && $block['url'] != '')
            {
                $link = ' onClick="window.location=\'' . $block['url'] . '\'" ';
                $cursor = 'cursor:pointer; ';
            }
            else
            {
                $link = null;
                $cursor = null;
            }
            
            $html[] = '<div title="' . $title . '"' . $link . ' style="' . $cursor . 'float:left; position: relative; width: ' . $width . '%; height: 100%; background-color: ' . $this->get_color(Translation :: get($block['type'])) . ';">';
            $html[] = '</div>';
            
            $prev_stop_date = $end_date;
        }
        
        return implode("\n", $html);
    
    }
}
?>