<?php
/* from the std

"BYxxx rule parts modify the recurrence in some manner. BYxxx rule parts for a period of time which is the same or greater than the frequency generally reduce or limit the number of occurrences of the recurrence generated. For example, "FREQ=DAILY;BYMONTH=1" reduces the number of recurrence instances from all days (if BYMONTH tag is not present) to all days in January. BYxxx rule parts for a period of time less than the frequency generally increase or expand the number of occurrences of the recurrence. For example, "FREQ=YEARLY;BYMONTH=1,2" increases the number of days within the yearly recurrence set from 1 (if BYMONTH tag is not present) to 2.

If multiple BYxxx rule parts are specified, then after evaluating the specified FREQ and INTERVAL rule parts, the BYxxx rule parts are applied to the current set of evaluated occurrences in the following order: BYMONTH, BYWEEKNO, BYYEARDAY, BYMONTHDAY, BYDAY, BYHOUR, BYMINUTE, BYSECOND and BYSETPOS; then COUNT and UNTIL are evaluated."

We will use two kinds of functions - those that restrict the date to allowed values and those that expand allowed values
*/
class IcalRecurrence
{
    private $event;
    private $mArray_end;
    private $mArray_start;
    private $count;
    private $interval;
    private $start_date;
    private $end_range;
    private $until;
    private $occurence_times;
    
    const ICAL_FREQUENCY = 'FREQ';
    const ICAL_DAYS = 'BYDAY';
    const ICAL_MONTHDAYS = 'BYMONTHDAY';
    const ICAL_MONTH = 'BYMONTH';
    const ICAL_INTERVAL = 'INTERVAL';
    const ICAL_COUNT = 'COUNT';
    const ICAL_UNTIL = 'UNTIL';
    const ICAL_YEARDAY = 'BYYEARDAY';
    const ICAL_WEEK_NO = 'BYWEEKNO';
    const ICAL_SETPOS = 'BYSETPOS';
    const ICAL_EXCEPT_DATE = 'EXDATE';
    const ICAL_WEEK_START = 'WKST';
    const ICAL_DURATION = 'DURATION';
    
    const NO_REPEAT = 'NONE';
    const REPEAT_TYPE_DAY = 'DAILY';
    const REPEAT_TYPE_WEEK = 'WEEKLY';
    const REPEAT_TYPE_MONTH = 'MONTHLY';
    const REPEAT_TYPE_YEAR = 'YEARLY';
    
    const OCCURENCE_START = 'start';
    const OCCURENCE_END = 'end';
    
    const DEBUG = true;

    function IcalRecurrence(vevent $event, $from_date, $to_date)
    {
        $this->event = $event;
        $this->mArray_start = $from_date;
        $this->mArray_end = $to_date;
        $this->occurences = array();
        
        if (self :: DEBUG)
        {
            echo '<tr><td>$this->event->summary</td><td>' . $this->event->summary['value'] . '</td><td></td></tr>';
            echo '<tr><td>$this->get_start_date()</td><td>' . $this->get_start_date() . '</td><td>' . date('r', $this->get_start_date()) . '</td></tr>';
            echo '<tr><td>$this->get_end_date()</td><td>' . $this->get_end_date() . '</td><td>' . date('r', $this->get_end_date()) . '</td></tr>';
            //            echo '<tr><td>View begin</td><td>' . $this->mArray_start . ' (' . date('r', $this->mArray_start) . ')</td></tr>';
        //            echo '<tr><td>View end</td><td>' . $this->mArray_end . ' (' . date('r', $this->mArray_end) . ')</td></tr>';
        //            echo '<tr><td>Event</td><td>';
        //            echo '<pre>';
        //            echo print_r($this->event, true);
        //            echo '</pre>';
        //            echo '</td>';
        //            echo '</tr>';
        }
    }

    /**
     * @return the $interval
     */
    public function get_interval()
    {
        if (! isset($this->interval))
        {
            if (is_array($this->get_event()->rrule) && isset($this->get_event()->rrule[0]['value'][self :: ICAL_INTERVAL]))
            {
                $this->interval = trim($this->get_event()->rrule[0]['value'][self :: ICAL_INTERVAL]);
            }
            else
            {
                $this->interval = 1;
            }
        }
        return $this->interval;
    }

    function set_interval($interval)
    {
        $this->interval = $interval;
    }

    function get_until()
    {
        return $this->until;
    }

    function get_occurence_times()
    {
        return $this->occurence_times;
    }

    function set_occurence_times($occurence_times)
    {
        $this->occurence_times = $occurences;
    }

    function add_occurence_time($occurence_time, $check_for_doubles = true)
    {
        if (($check_for_doubles === true && ! in_array($occurence_time)) || $check_for_doubles === false)
        {
            $this->occurence_times[] = $occurence_time;
        }
    }

    function set_until($until)
    {
        $this->until = $until;
    }

    /**
     * @return the $count
     */
    public function get_count()
    {
        if (! isset($this->count))
        {
            if (is_array($this->get_event()->rrule) && isset($this->get_event()->rrule[0]['value'][self :: ICAL_COUNT]))
            {
                $this->count = trim($this->get_event()->rrule[0]['value'][self :: ICAL_COUNT]);
            }
            else
            {
                $this->count = 1000000;
            }
        }
        return $this->count;
    }

    function set_count($count)
    {
        $this->count = $count;
    }

    function get_event()
    {
        return $this->event;
    }

    function set_event($event)
    {
        $this->event = $event;
    }

    function set_start_date($start_date)
    {
        $this->start_date = $start_date;
    }

    function get_end_range()
    {
        return $this->end_range;
    }

    function set_end_range($end_range)
    {
        $this->end_range = $end_range;
    }

    function set_correct_time($timestamp)
    {
        $day = date('d', $timestamp);
        $month = date('m', $timestamp);
        $year = date('Y', $timestamp);
        
        $hour = date('H', $this->get_start_date());
        $minute = date('i', $this->get_start_date());
        $second = date('s', $this->get_start_date());
        
        return mktime($hour, $minute, $second, $month, $day, $year);
    }

    function get_occurences()
    {
        $end_date = $this->get_end_date();
        $start_date = $this->get_start_date();
        
        if (! isset($start_date))
        {
            $this->set_start_date(0);
        }
        
        if (! isset($end_date))
        {
            $duration = $this->get_duration();
            if (! isset($duration))
            {
                $duration = 0;
            }
            $end_date = $this->get_start_date() + $duration;
        }
        
        $this->set_end_range($this->get_marray_end());
        
        if (self :: DEBUG)
        {
            echo '<tr><td>$this->end_range</td><td>' . $this->get_end_range() . '</td><td>' . date('r', $this->get_end_range()) . '</td></tr>';
        }
        
        $next_range = $this->get_start_date();
        
        if ($this->get_count() == 1000000 && $this->get_interval() == 1 && $this->get_marray_start() > $next_range)
        {
            while ($next_range < $this->get_marray_start())
            {
                $next_range = strtotime('+' . $this->get_interval() . ' ' . $this->get_freq_type_name(), $next_range);
            }
            //            $next_range = $this->get_marray_start();
        }
        
        if ($next_range < $this->get_start_date())
        {
            $next_range = $this->get_start_date();
        }
        
        if (self :: DEBUG)
            echo '<tr><td>initial $next_range</td><td>' . $next_range . '</td><td>' . date('r', $next_range) . '</td></tr>';
        
        $this->set_until($this->get_until_unixtime());
        $until = $this->get_until();
        if (isset($until) && $this->get_end_range() > $until)
        {
            $this->set_end_range($until);
        }
        else
        {
            $this->set_until($this->get_marray_end());
        }
        
        $freq_type = $this->get_freq_type();
        switch ($freq_type)
        {
            case self :: REPEAT_TYPE_WEEK :
                $next_range = $this->set_correct_time(strtotime("this " . date("D", $this->get_start_date()), $next_range));
                break;
            case self :: REPEAT_TYPE_YEAR :
                $end_range += 366 * 24 * 60 * 60;
                break;
        }
        
        if (! $this->repeats() && isset($end_date))
        {
            $this->set_end_range($end_date);
            $this->set_count(1);
        }
        
        if (self :: DEBUG)
        {
            echo '<tr><td>$this->event->rrule[\'count\']</td><td>' . $this->get_count() . '</td><td></td></tr>';
            echo '<tr><td>$this->event->rrule[\'interval\']</td><td>' . $this->get_interval() . '</td><td></td></tr>';
        }
        
        $occurences = array();
        while ($next_range <= $this->get_end_range() && $this->get_count() > 0)
        {
            
            if (self :: DEBUG)
            {
                echo '<tr><td colspan="3" style="background-color: #b5cae7;"></td></tr>';
                echo '<tr><td>$next_range</td><td>' . $next_range . '</td><td>' . date('r', $next_range) . '</td></tr>';
                echo '<tr><td>$count</td><td>' . $this->get_count() . '</td><td></td></tr>';
            }
            
            $year = date("Y", $next_range);
            $month = date("m", $next_range);
            //            $time = mktime(12, 0, 0, $month, date("d", $this->get_start_date()), $year);
            $time = $this->set_correct_time($next_range);
            //            $time = mktime(date("H", $this->get_start_date()), date("i", $this->get_start_date()), date("s", $this->get_start_date()), $month, date("d", $this->get_start_date()), $year);
            

            switch ($this->get_freq_type())
            {
                case self :: REPEAT_TYPE_DAY :
                    $this->add_recur($next_range);
                    break;
                case self :: REPEAT_TYPE_WEEK :
                    $day = $this->expand_byday($next_range, $year, $month);
                    $test = $this->add_recur($day);
                    $occurences = array_merge($occurences, $test);
                    break;
                case self :: REPEAT_TYPE_MONTH :
                    $bymonthday = $this->get_bymonthday();
                    if (! empty($bymonthday))
                    {
                        $time = mktime(12, 0, 0, $month, 1, $year);
                    }
                    $times = $this->expand_bymonthday(array($time));
                    foreach ($times as $time)
                    {
                        $occurences = array_merge($occurences, $this->add_recur($this->expand_byday($time, $year, $month)));
                    }
                    break;
                case self :: REPEAT_TYPE_YEAR :
                    $times = $this->expand_bymonth($time, $year);
                    $times = $this->expand_byweekno($times, $year);
                    $times = $this->expand_byyearday($times, $year);
                    $times = $this->expand_bymonthday($times, $year);
                    foreach ($times as $time)
                    {
                        $occurences = array_merge($occurences, $this->add_recur($this->expand_byday($time, $year, $month)));
                    }
                    break;
                default :
                    $occurences = array_merge($occurences, $this->add_recur($this->get_start_date()));
                    break 2;
            }
            
            //            echo '+' . $this->get_interval() . ' ' . $this->get_freq_type_name() . '<br />';
            

            $next_range = strtotime('+' . $this->get_interval() . ' ' . $this->get_freq_type_name(), $next_range);
        }
        
        if (self :: DEBUG)
        {
            echo '<tr><td colspan="3" style="background-color: #b5cae7;"></td></tr>';
            echo '<tr><td>$occurences</td><td colspan="2">' . print_r($occurences, true) . '</td></tr>';
        }
        
        $occurence_times = $this->get_occurence_times();
        
        $occurences = array();
        $length = $this->get_end_date() - $this->get_start_date();
        
        foreach ($occurence_times as $key => $occurence_time)
        {
            $occurence = array();
            $occurence[self :: OCCURENCE_START] = $occurence_time;
            $occurence[self :: OCCURENCE_END] = $occurence_time + $event_length;
            $occurences[] = $occurence;
            
            if (self :: DEBUG)
            {
                echo '<tr><td>$event_occurence_' . $key . '->begin</td><td>' . $occurence[self :: OCCURENCE_START] . '</td><td>' . date('r', $occurence[self :: OCCURENCE_START]) . '</td></tr>';
                echo '<tr><td>$event_occurence_' . $key . '->end</td><td>' . $occurence[self :: OCCURENCE_END] . '</td><td>' . date('r', $occurence[self :: OCCURENCE_END]) . '</td></tr>';
            }
        
        }
        
        return $occurences;
    }

    function get_start_date()
    {
        if (! isset($this->start_date))
        {
            $start_date = $this->get_event()->dtstart['value'];
            $this->start_date = mktime($start_date['hour'], $start_date['min'], $start_date['sec'], $start_date['month'], $start_date['day'], $start_date['year']);
        }
        return $this->start_date;
    }

    function get_end_date()
    {
        $end_date = $this->get_event()->dtend['value'];
        return mktime($end_date['hour'], $end_date['min'], $end_date['sec'], $end_date['month'], $end_date['day'], $end_date['year']);
    }

    function repeats()
    {
        if (! empty($this->get_event()->rrule))
        {
            return true;
        }
        return false;
    }

    function repeats_indefinately()
    {
        $repeat_to = $this->get_repeat_to();
        return ($repeat_to == '0');
    }

    function get_duration()
    {
        return $this->get_event()->duration;
    }

    /**
     * @return the $bymonth
     */
    public function get_bymonth()
    {
        if (is_array($this->get_event()->rrule))
        {
            return $this->get_event()->rrule[0]['value'][self :: ICAL_MONTH];
        }
        else
        {
            return null;
        }
    }

    /**
     * @return the $byweekno
     */
    public function get_byweekno()
    {
        if (is_array($this->get_event()->rrule))
        {
            return $this->get_event()->rrule[0]['value'][self :: ICAL_WEEK_NO];
        }
        else
        {
            return null;
        }
    }

    /**
     * @return the $bymonthday
     */
    public function get_bymonthday()
    {
        if (is_array($this->get_event()->rrule))
        {
            return $this->get_event()->rrule[0]['value'][self :: ICAL_MONTHDAYS];
        }
        else
        {
            return null;
        }
    }

    /**
     * @return the $freq_type
     */
    public function get_freq_type()
    {
        if (is_array($this->get_event()->rrule))
        {
            return trim($this->get_event()->rrule[0]['value'][self :: ICAL_FREQUENCY]);
        }
        else
        {
            return self :: NO_REPEAT;
        }
    }

    public function get_freq_type_name()
    {
        $freq_type = $this->get_freq_type();
        switch ($freq_type)
        {
            case self :: REPEAT_TYPE_YEAR :
                $freq_type = 'year';
                break;
            case self :: REPEAT_TYPE_MONTH :
                $freq_type = 'month';
                break;
            case self :: REPEAT_TYPE_WEEK :
                $freq_type = 'week';
                break;
            case self :: REPEAT_TYPE_DAY :
                $freq_type = 'day';
                break;
            //            case 'HOURLY' :
        //                $freq_type = 'hour';
        //                break;
        //            case 'MINUTELY' :
        //                $freq_type = 'minute';
        //                break;
        //            case 'SECONDLY' :
        //                $freq_type = 'second';
        //                break;
        }
        return $freq_type;
    }

    /**
     * @return the $marray_begin
     */
    public function get_marray_start()
    {
        return $this->mArray_start;
    }

    /**
     * @return the $marray_end
     */
    public function get_marray_end()
    {
        return $this->mArray_end;
    }

    /**
     * @return the $except_dates
     */
    public function get_except_dates()
    {
        return $this->get_event()->exdate;
    }

    /**
     * @return the $until_unixtime
     */
    public function get_until_unixtime()
    {
        if (is_array($this->get_event()->rrule) && is_array($this->get_event()->rrule[0]['value'][self :: ICAL_UNTIL]))
        {
            $repeat_date = $this->get_event()->rrule[0]['value'][self :: ICAL_UNTIL];
            //Correct repeat date for ical, don't take time into account
            return mktime(23, 59, 59, $repeat_date['month'], $repeat_date['day'], $repeat_date['year']);
        }
    }

    /**
     * @return the $wkst
     */
    public function get_wkst($convert = false, $text = true)
    {
        if (isset($this->get_event()->rrule[0]['value'][self :: ICAL_WEEK_START]))
        {
            $wkst = $this->get_event()->rrule[0]['value'][self :: ICAL_WEEK_START];
        }
        else
        {
            $wkst = 'MO';
        }
        
        if ($convert)
        {
            return $this->convert_day_name($wkst, $text);
        }
        else
        {
            return $wkst;
        }
    }

    /**
     * @return the $byyearday
     */
    public function get_byyearday()
    {
        if (is_array($this->get_event()->rrule))
        {
            return $this->get_event()->rrule[0]['value'][self :: ICAL_YEARDAY];
        }
        else
        {
            return null;
        }
    }

    /**
     * @return the $byday
     */
    public function get_byday()
    {
        if (is_array($this->get_event()->rrule))
        {
            foreach ($this->get_event()->rrule[0]['value'][self :: ICAL_DAYS] as $day)
            {
                $days[] = $day['DAY'];
            }
            return $days;
        }
        else
        {
            return null;
        }
    }

    public function get_bysetpos()
    {
        if (is_array($this->get_event()->rrule))
        {
            return $this->get_event()->rrule[0]['value'][self :: ICAL_SETPOS];
        }
        else
        {
            return null;
        }
    }

    function add_recur($times, $freq = '')
    {
        if (self :: DEBUG)
        {
            echo '<tr><td>ORIGINAL $this->add_recur()->times</td><td colspan="2">' . print_r($times, true) . '</td></tr>';
        }
        //        $repeats = array();
        $mArray_begin = $this->get_marray_start();
        $mArray_end = $this->get_marray_end();
        
        if (! is_array($times))
            $times = array($times);
        
        $times = $this->restrict_bymonth($times);
        $times = $this->restrict_byyearday($times);
        $times = $this->restrict_bymonthday($times);
        $times = $this->restrict_byday($times);
        
        //        if ($this->get_start_date() > $mArray_begin && ! in_array($this->get_start_date(), $times))
        //        {
        //            $times[] = $this->get_start_date();
        //        }
        

        $times = $this->restrict_bysetpos($times);
        $times = array_unique($times);
        sort($times);
        
        if (self :: DEBUG)
        {
            echo '<tr><td>PROCESSED $this->add_recur()->times</td><td colspan="2">' . print_r($times, true) . '</td></tr>';
        }
        
        //$until_date = date("Ymd", $this->get_end_range());
        

        foreach ($times as $time)
        {
            if (! isset($time) || $time == '')
            {
                continue;
            }
            // $date = date("Ymd", $time);
            //dump($time);
            //$time = strtotime("$date 12:00:00");
            //dump($time);
            //            if (date("Ymd", $time) != $this->get_start_date())
            //                $time = $time + $this->day_offset * (24 * 60 * 60);
            // ! in_array($date, $this->get_except_dates())
            if (isset($time) && $time != '' && ! in_array($time, $repeats) && $time <= $this->get_until() && $time >= $this->get_start_date())
            {
                $this->set_count($this->get_count() - 1);
                if ($time >= $mArray_begin && $time <= $mArray_end && $this->get_count() >= 0)
                {
                    
                    $this->add_occurence_time($time);
                }
            
            }
        }
        //        return $repeats;
    }

    function expand_bymonth($time, $year)
    {
        $bymonth = $this->get_bymonth();
        $byweekno = $this->get_byweekno();
        $bymonthday = $this->get_bymonthday();
        
        if (! empty($byweekno))
            return $time;
        if (empty($bymonth))
            $bymonth = array(date("m", $this->get_start_date()));
        $d = date("d", $this->get_start_date());
        if (! empty($bymonthday))
            $d = 1;
        foreach ($bymonth as $m)
        {
            $time = mktime(12, 0, 0, $m, $d, $year);
            $times[] = $time;
        }
        return $times;
    }

    function expand_byweekno($times, $year)
    {
        $byweekno = $this->get_byweekno();
        $freq_type = $this->get_freq_type();
        
        if ($freq_type != self :: REPEAT_TYPE_YEAR)
            return $times;
        if (empty($byweekno))
            return $times;
        $total_weeks = date("W", mktime(12, 0, 0, 12, 24, $year)) + 1;
        $w1_start = strtotime("this $this->get_wkst(true)", mktime(12, 0, 0, 1, 1, $year));
        foreach ($byweekno as $weekno)
        {
            if ($weekno < 0)
                $weekno = $weekno + $total_weeks;
            $new_times[] = strtotime("+" . (($weekno - 1) * 7) . "days", $w1_start);
        }
        return $new_times;
    }

    function expand_byyearday($times, $year)
    {
        $byyearday = $this->get_byyearday();
        if (empty($byyearday))
            return $times;
        $py = $year - 1;
        $ny = $year + 1;
        $new_times = array();
        foreach ($times as $time)
        {
            foreach ($byyearday as $yearday)
            {
                if ($yearday > 0)
                {
                    $day = strtotime("+$yearday days Dec 31, $py");
                }
                else
                    $day = strtotime("Jan 1 $ny $yearday days");
                if (date("Y", $day == $year))
                    $new_times[] = $day;
            }
        }
        return $new_times;
    }

    function expand_bymonthday($times, $year)
    {
        $bymonthday = $this->get_bymonthday();
        if (empty($bymonthday))
            return $times;
        foreach ($times as $time)
        {
            $month = date('m', $time);
            foreach ($bymonthday as $monthday)
            {
                if ($monthday < 0)
                    $monthday = date("t", $time) + $monthday + 1;
                $new_times[] = mktime(12, 0, 0, $month, $monthday, $year);
            }
        }
        return $new_times;
    }

    function dateOfWeek($time, $day)
    {
        $week_start_day = 'Monday';
        $num = date('w', strtotime($week_start_day));
        $start_day_time = strtotime((date('w', $time) == $num ? "$week_start_day" : "last $week_start_day"), $time);
        $time = strtotime($day, $start_day_time);
        $time += (12 * 60 * 60);
        return $time;
    }

    function expand_byday($time, $year, $month)
    {
        $byday = $this->get_byday();
        $byweekno = $this->get_byweekno();
        $freq_type = $this->get_freq_type();
        $bymonth = $this->get_bymonth();
        if (empty($byday))
        {
            return array($time);
        }
        $times = array();
        $the_sunday = $this->dateOfWeek($time, $this->get_wkst(true));
        
        foreach ($byday as $key => $day)
        {
            ereg('([-\+]{0,1})?([0-9]+)?([A-Z]{2})', $day, $byday_arr);
            $on_day = $this->convert_day_name($byday_arr[3]);
            switch ($freq_type)
            {
                case self :: REPEAT_TYPE_WEEK :
                    echo ('hier week');
                    $next_date_time = strtotime("this $on_day", strtotime($the_sunday)) + (12 * 60 * 60);
                    $times[] = $next_date_time;
                    break;
                case self :: REPEAT_TYPE_MONTH :
                    $time = mktime(12, 0, 0, $month, 1, $year);
                case self :: REPEAT_TYPE_YEAR :
                    if (empty($byweekno))
                    {
                        $week_arr = array(1, 2, 3, 4, 5);
                        if (isset($byday_arr[2]) && $byday_arr[2] != '')
                            $week_arr = array($byday_arr[2]);
                        $month_start = strtotime(date("Ym01", $time)) - (24 * 60 * 60);
                        $month_end = strtotime(date("Ymt", $time)) + (36 * 60 * 60);
                        if ($freq_type == 'year' && empty($bymonth))
                        {
                            $month_start = mktime(12, 0, 0, 1, 0, $year);
                            $month_end = mktime(12, 0, 0, 1, 1, $year + 1);
                        }
                        $month_start_day = strtolower(date("D", $month_start));
                        foreach ($week_arr as $week)
                        {
                            if ($byday_arr[1] == '-')
                                $next_date_time = strtotime($byday_arr[1] . $week . $on_day, $month_end);
                            else
                            {
                                $special_offset = ($month_start_day == $on_day) ? (24 * 60 * 60) : 0;
                                $next_date_time = strtotime($byday_arr[1] . $week . $on_day, ($month_start + $special_offset));
                            }
                            if (date("m", $next_date_time) == date("m", $time))
                                $times[] = $next_date_time;
                        }
                    }
                    else
                    {
                        $next_date_time = strtotime("this $on_day", strtotime($the_sunday)) + (12 * 60 * 60);
                        $times[] = $next_date_time;
                    }
                    break;
                default :
                    $month_start = strtotime(date("Ym01", $time));
                    $next_date_time = strtotime($byday_arr[1] . $byday_arr[2] . $on_day, $month_start);
            }
        }
        return $times;
    }

    function restrict_bymonth($times)
    {
        $bymonth = $this->get_bymonth();
        $byyearday = $this->get_byyearday();
        if (empty($bymonth) || ! empty($byyearday))
            return $times;
        $new_times = array();
        foreach ($times as $time)
        {
            if (in_array(date("m", $time), $bymonth))
                $new_times[] = $time;
        }
        return $new_times;
    }

    function restrict_byweekno($times)
    {
        $byweekno = $this->get_byweekno();
        if (empty($byweekno))
            return $times;
        $new_times = array();
        foreach ($times as $time)
            if (in_array(date("W", $time), $byweekno))
                $new_times[] = $time;
        return $new_times;
    
    }

    function restrict_byyearday($times)
    {
        $byyearday = $this->get_byyearday();
        if (empty($byyearday))
            return $times;
        $new_times = array();
        foreach ($times as $time)
        {
            foreach ($byyearday as $yearday)
            {
                if ($yearday < 0)
                {
                    $yearday = 365 + $yearday + 1;
                    if (date("L", $time))
                        $yearday += 1;
                }
                $yearday_arr[] = $yearday;
            }
            # date(z,$time) gives 0 for Jan 1
            if (in_array((date("z", $time) + 1), $yearday_arr))
                $new_times[] = $time;
        }
        return $new_times;
    }

    function restrict_bymonthday($times)
    {
        $bymonthday = $this->get_bymonthday();
        if (empty($bymonthday))
            return $times;
        $new_times = array();
        foreach ($times as $time)
        {
            foreach ($bymonthday as $monthday)
            {
                if ($monthday < 0)
                    $monthday = date("t", $time) + $monthday + 1;
                $monthday_arr[] = $monthday;
            }
            if (in_array(date("j", $time), $monthday_arr))
                $new_times[] = $time;
        }
        return $new_times;
    }

    function restrict_byday($times)
    {
        $byday = $this->get_byday();
        if (empty($byday))
            return $times;
        foreach ($byday as $key => $day)
        {
            ereg('([-\+]{0,1})?([0-9]{1})?([A-Z]{2})', $day, $byday_arr);
            $byday3[] = $this->convert_day_name($byday_arr[3]);
        }
        $new_times = array();
        foreach ($times as $time)
            if (in_array(strtolower(date("D", $time)), $byday3))
                $new_times[] = $time;
        return $new_times;
    }

    function restrict_bysetpos($times)
    {
        $bysetpos = $this->get_bysetpos();
        if (empty($bysetpos))
            return $times;
        sort($times);
        $new_times = array();
        foreach ($bysetpos as $setpos)
        {
            $new_times[] = implode('', array_slice($times, $setpos, 1));
        }
        return $new_times;
    }

    function convert_day_name($day, $txt = true)
    {
        switch ($day)
        {
            case 'SU' :
                return ($txt ? 'sun' : '0');
            case 'MO' :
                return ($txt ? 'mon' : '1');
            case 'TU' :
                return ($txt ? 'tue' : '2');
            case 'WE' :
                return ($txt ? 'wed' : '3');
            case 'TH' :
                return ($txt ? 'thu' : '4');
            case 'FR' :
                return ($txt ? 'fri' : '5');
            case 'SA' :
                return ($txt ? 'sat' : '6');
        }
    }
}
?>
