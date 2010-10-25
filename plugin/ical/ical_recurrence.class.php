<?php
/**
 * Based on phpicalendar 2.4
 * http://phpicalendar.sourceforge.net/
 * phpicalendar is distributed under the GPL.
 *
 * Adapted for Chamilo by:
 * @author Magali Gillard
 * @author Hans De Bisschop
 */

class IcalRecurrence
{
    private $event;
    private $view_end;
    private $view_start;
    private $count;
    private $interval;
    private $start_date;
    private $end_range;
    private $until;
    private $occurence_times = array();

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
    const OCCURENCE_END = 'end';
    const OCCURENCE_START = 'start';

    const NO_REPEAT = 'NONE';
    const REPEAT_TYPE_DAY = 'DAILY';
    const REPEAT_TYPE_WEEK = 'WEEKLY';
    const REPEAT_TYPE_MONTH = 'MONTHLY';
    const REPEAT_TYPE_YEAR = 'YEARLY';

    private $debug = false;

    function IcalRecurrence(vevent $event, $from_date, $to_date)
    {
    	//$from_date = ($from_date + $to_date) / 2;
        //$this_day = date('d', $from_date);
        //$this_month = date('m', $from_date);
        //$this_year = date('Y', $from_date);

        //For the list view
        $date = getdate();
        $date_unixtime = mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']);
        // TODO: List view needs to start today, not on the last selected date.
//    	$from_date = ($date_unixtime + $to_date) / 2;
    	$this_day = date('d', $from_date);
        $this_month = date('m', $from_date);
        $this_year = date('Y', $from_date);
//    	$this_day = $date['mday'];
//    	$this_month = $date['mon'];
//    	$this_year = $date['year'];

        $start_month = $date['mon'] - 1;
        $start_year = $date['year'];
        $end_month = $this_month + 1;
        $end_year = $this_year;


        if ($this_month == 1)
        {
            $start_month = 12;
            $start_year --;
        }
        if ($this_month == 12)
        {
            $end_month = 1;
            $end_year ++;
        }

        $this->event = $event;
        $this->view_start = mktime(0, 0, 0, $start_month, 1, $start_year);
        $this->view_end = mktime(0, 0, 0, $end_month, 31, $end_year);

        if ($this->debug)
        {
            echo '<hr />';
            echo '<b>ICAL RECURRENCE</b>';
            echo '<hr />';
            echo '<table class="data_table">';
            echo '<thead>';
            echo '<tr><th>Variable</td><th>Value</td>';
            echo '</thead>';
            echo '<tbody>';
            echo '<tr><td>Title</td><td>' . $this->event->summary['value'] . '</td>';
            echo '<tr><td>View begin</td><td>' . $this->view_start . ' (' . date('r', $this->view_start) . ')</td>';
            echo '<tr><td>View end</td><td>' . $this->view_end . ' (' . date('r', $this->view_end) . ')</td>';
            //            echo '<tr><td>Event</td><td>';
            //            echo '<pre>';
            //            echo print_r($this->event, true);
            //            echo '</pre>';
            //            echo '</td>';
            //            echo '</tr>';
            echo '</tbody>';
            echo '</table>';
        }
    }

    function get_repeat()
    {
    	$html = array();
    	$html[] = Translation :: get('Repeats') . ' ';
    	$html[] = Translation :: get(ucfirst(strtolower($this->get_freq_type())));

    	if ($this->get_interval() > 1)
    	{
    		$html[] = Translation :: get('Every') . ' ' . $this->get_interval() . ' ' . Translation :: get(ucfirst($this->get_freq_type_name() . 's'));
    	}

    	if ($this->get_count() > 1 && $this->get_count() != 1000000)
    	{
    		$html[] = ' '. $this->get_count() . ' '. Translation :: get('Times');
    	}
    	else
    	{
    		$html[] = ' ' . Translation :: get('Once');
    	}
    	$date_format = Translation :: get('dateFormatShort');

    	if ($this->get_until_unixtime())
    	{
    		$html[] = Translation :: get('Until');
    		$html[] = Text :: format_locale_date($date_format, $this->get_until_unixtime());
    	}

    	if ($this->get_byday())
    	{
    		$html[] = Translation :: get('On');
    		$days = array();
    		$DaysLong = array(Translation :: get("SundayLong"), Translation :: get("MondayLong"), Translation :: get("TuesdayLong"), Translation :: get("WednesdayLong"), Translation :: get("ThursdayLong"), Translation :: get("FridayLong"), Translation :: get("SaturdayLong"));
    		foreach($this->get_byday() as $byday)
    		{
    			ereg('([-\+]{0,1})?([0-9]+)?([A-Z]{2})', $byday, $byday_arr);
    			$day_number = $this->convert_day_name($byday_arr[3], false);
    			if ($byday_arr[2])
    			{
    				$rank = $byday_arr[2];
    				if ($byday_arr[1])
    				{
    					if ($byday_arr[2] == 1)
    					{
    						$days[] = Translation::get('Last') . ' ' . $DaysLong[$day_number];
    					}
    					else
    					{
    						$days[] = Text :: ordinal_suffix($rank) . ' ' . Translation::get('ToLast') . ' ' . $DaysLong[$day_number];
    					}
    				}
    				else
    				{
    					$rank = $byday_arr[2];
    					$days[] = Text :: ordinal_suffix($rank) . ' ' . $DaysLong[$day_number];
    				}
    			}
    			else
    			{
    				$days [] = $DaysLong[$day_number];
    			}
    		}
    		$html[] = implode(", ", $days);
    	}

    	if ($this->get_bymonthday())
    	{
    		$monthdays = array();
    		foreach($this->get_bymonthday() as $bymonthday)
    		{
    			$monthdays[] = $bymonthday;
    		}
    		$html[] = implode(", ", $monthdays);
    	}

    	if ($this->get_bymonth())
    	{
    		$html[] = Translation :: get('In');
    		$months = array();
    		$MonthsLong = array(Translation :: get("JanuaryLong"), Translation :: get("FebruaryLong"), Translation :: get("MarchLong"), Translation :: get("AprilLong"), Translation :: get("MayLong"), Translation :: get("JuneLong"), Translation :: get("JulyLong"), Translation :: get("AugustLong"), Translation :: get("SeptemberLong"), Translation :: get("OctoberLong"), Translation :: get("NovemberLong"), Translation :: get("DecemberLong"));
    		foreach($this->get_bymonth() as $bymonth)
    		{
    			$months[] = $MonthsLong[$bymonth-1];
    		}
    		$html[] = implode(", ", $months);
    	}

    	return implode("\n", $html);
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

    function get_occurences()
    {
    	$end_date_timestamp = $this->get_end_date();
        $start_date_timestamp = $this->get_start_date();
        if (! isset($start_date_timestamp))
        {
            $this->set_start_date(0);
        }

        if (! isset($end_date_timestamp))
        {
            $duration = $this->get_duration();
            if (! isset($duration))
            {
                $duration = 0;
            }
            $end_date_timestamp = $this->get_start_date() + $duration;
        }
        $start_time = date('Hi', $this->get_start_date());
        $end_time = date('Hi', $end_date_timestamp);

        $length = $this->get_end_date() - $this->get_start_date();
        if ($length < 0)
        {
            $length = 0;
            $end_time = $start_time;
        }
        //$this->get_view_end() = 31/03/1995
        //$this->get_end_range() = 1/1/1970
        $this->set_end_range($this->get_view_end() + 60 * 60 * 24);

        $next_range = $this->get_start_date(true, true);

        if ($this->get_count() == 1000000 && $this->get_interval() == 1 && $this->get_view_start() > $next_range)
        {
            $next_range = $this->get_view_start();
        }

        if ($next_range < $this->get_start_date(true, true))
        {
            $next_range = $this->get_start_date(true, true);
        }

        $this->set_until($this->get_until_unixtime());
        $until = $this->get_until();
        if (isset($until) && $this->get_end_range() > $until)
        {
            $this->set_end_range($until);
        }
        else
        {
            $this->set_until($this->get_view_end());
        }

        $freq_type = $this->get_freq_type();
        switch ($freq_type)
        {
            case self :: REPEAT_TYPE_WEEK :
                //$next_range = $this->set_correct_time(strtotime("this " . date("D", $this->get_start_date()), $next_range));
                $next_range = strtotime("this " . date("D", $this->get_start_date(true, true)), $next_range);
                break;
            case self :: REPEAT_TYPE_YEAR :
                $this->set_end_range($this->get_end_range() + (366 * 24 * 60 * 60));
                break;
        }

        /*if ($this->get_view_start() > $this->get_start_date() && $this->get_end_date() > $this->get_view_start())
        {
            $next_range = $this->get_start_date();
        }*/

        if (! $this->repeats() && isset($end_date_timestamp))
        {
            $this->set_end_range(strtotime($this->get_end_date(false)));
            $this->set_count(1);
        }

        $repeats = array();
        while ($next_range <= $this->get_end_range() && $this->get_count() > 0)
        {
        	$year = date("Y", $next_range);
            $month = date("m", $next_range);
            $time = mktime(12, 0, 0, $month, date("d", $this->get_start_date()), $year);
            //$time = $this->set_correct_time($next_range);
            switch ($this->get_freq_type())
            {
                case self :: REPEAT_TYPE_DAY :
                    $this->add_recur($next_range);
                    break;
                case self :: REPEAT_TYPE_WEEK :
                    $day = $this->expand_byday($next_range, $year, $month);
                    $this->add_recur($day);
                    break;
                case self :: REPEAT_TYPE_MONTH :
                    $bymonthday = $this->get_bymonthday();
                    if (! empty($bymonthday))
                    {
                        $time = mktime(12, 0, 0, $month, 1, $year);
                    }
                    $times = $this->expand_bymonthday(array($time), $year);

                    foreach ($times as $time)
                    {
                    	$this->add_recur($this->expand_byday($time, $year, $month));
                    }
                    break;
                case self :: REPEAT_TYPE_YEAR :
                    $times = $this->expand_bymonth($time, $year);
                    $times = $this->expand_byweekno($times, $year);
                    $times = $this->expand_byyearday($times, $year);
                    $times = $this->expand_bymonthday($times, $year);
                    foreach ($times as $time)
                    {
                        $this->add_recur($this->expand_byday($time, $year, $month));
                    }
                    break;
                default :
                    $this->add_recur($this->get_start_date());
                    break 2;
            }
            $next_range = strtotime('+' . $this->get_interval() . ' ' . $this->get_freq_type_name(), $next_range);
        }
        $occurence_times = $this->get_occurence_times();
        $occurences = array();

        $occurences_hours = date('H', $this->get_start_date());
        $occurences_minutes = date('i', $this->get_start_date());

        foreach ($occurence_times as $occurence_time)
        {
            $occurence_year = date('Y', $occurence_time);
            $occurence_month = date('m', $occurence_time);
            $occurence_day = date('d', $occurence_time);
            $occurence_date = date('Ymd', $occurence_time);

            $next_range = mktime($occurences_hours, $occurences_minutes, 0, $occurence_month, $occurence_day, $occurence_year);
            $end_range = $next_range + $length;
            $end_date_tmp = date('Ymd', $end_range);

            $start_date_time = strtotime($occurence_date . $start_time);
            $end_date_time = strtotime($end_date_tmp . $end_time);

            $occurence = array();
            $occurence[self :: OCCURENCE_START] = $start_date_time;
            $occurence[self :: OCCURENCE_END] = $end_date_time;
            $occurences[] = $occurence;
        }
        return $occurences;
    }

    function get_start_date($timestamp = true, $time_midnight = false)
    {
        if (! isset($this->start_date))
        {
            $start_date = $this->get_event()->dtstart['value'];
            $this->start_date = mktime($start_date['hour'], $start_date['min'], $start_date['sec'], $start_date['month'], $start_date['day'], $start_date['year']);
        }
        if ($timestamp)
        {
        	if ($time_midnight)
        	{
        		return strtotime(date('Ymd', $this->start_date . ' 00:00:00'));
        	}
        	else
        	{
        		return $this->start_date;
        	}
        }
        else
        {
        	return date('Ymd', $this->start_date);
        }
    }

    function get_end_date($timestamp = true)
    {
        $end_date = $this->get_event()->dtend['value'];
        $end_date = mktime($end_date['hour'], $end_date['min'], $end_date['sec'], $end_date['month'], $end_date['day'], $end_date['year']);
        return ($timestamp ? $end_date : date('Ymd', $end_date));
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
        if (is_array($this->get_event()->rrule) && isset($this->get_event()->rrule[0]['value'][self :: ICAL_MONTH]))
        {
            if (count($this->get_event()->rrule[0]['value'][self :: ICAL_MONTH]) > 1)
            {
                foreach ($this->get_event()->rrule[0]['value'][self :: ICAL_MONTH] as $month)
                {
                    $months[] = trim($month);
                }
            }
            else
            {
                $months[] = trim($this->get_event()->rrule[0]['value'][self :: ICAL_MONTH]);
            }
            return $months;
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
            return trim($this->get_event()->rrule[0]['value'][self :: ICAL_WEEK_NO]);
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
    	if (is_array($this->get_event()->rrule) && isset($this->get_event()->rrule[0]['value'][self :: ICAL_MONTHDAYS]))
        {
            if (count($this->get_event()->rrule[0]['value'][self :: ICAL_MONTHDAYS]) > 1)
            {
                foreach ($this->get_event()->rrule[0]['value'][self :: ICAL_MONTHDAYS] as $monthday)
                {
                	$monthdays[] = trim($monthday);
                }
            }
            else
            {
                $monthdays[] = trim($this->get_event()->rrule[0]['value'][self :: ICAL_MONTHDAYS]);
            }
            return $monthdays;
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
    public function get_view_start($timestamp = true)
    {
    	return ($timestamp ? $this->view_start : date('Ymd', $this->view_start));
    }

    /**
     * @return the $view_end
     */
    public function get_view_end($timestamp = true)
    {
    	return ($timestamp ? $this->view_end : date('Ymd', $this->view_end));
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
            return trim($this->get_event()->rrule[0]['value'][self :: ICAL_YEARDAY]);
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
        if (is_array($this->get_event()->rrule) && isset($this->get_event()->rrule[0]['value'][self :: ICAL_DAYS]))
        {
            if (! isset($this->get_event()->rrule[0]['value'][self :: ICAL_DAYS]['DAY']))
            {
                foreach ($this->get_event()->rrule[0]['value'][self :: ICAL_DAYS] as $day)
                {
                    $days[] = trim($day['0']) . trim($day['DAY']);
                }
            }
            else
            {
                $days[] = trim($this->get_event()->rrule[0]['value'][self :: ICAL_DAYS]['0']) . trim($this->get_event()->rrule[0]['value'][self :: ICAL_DAYS]['DAY']);
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
            return trim($this->get_event()->rrule[0]['value'][self :: ICAL_SETPOS]);
        }
        else
        {
            return null;
        }
    }

    public function get_occurence_times()
    {
        return $this->occurence_times;
    }

    public function set_occurence_times($occurence_times)
    {
        $this->occurence_times = $occurence_times;
    }

    public function add_occurence_times($time, $check_for_doubles = true)
    {
        if ($check_for_doubles === true && ! in_array($time, $this->get_occurence_times()))
        {
            $this->occurence_times[] = $time;
        }
    }

    function add_recur($times, $freq = '')
    {
    	$view_start = $this->get_view_start();
        $view_end = $this->get_view_end();
        if (! is_array($times))
            $times = array($times);

        $times = $this->restrict_bymonth($times);
        $times = $this->restrict_byyearday($times);
        $times = $this->restrict_bymonthday($times);
        $times = $this->restrict_byday($times);

        if ($this->get_start_date(true, true) > $view_start)
        {
            $times[] = $this->get_start_date(true, true);
        }

        $times = $this->restrict_bysetpos($times);
        $times = array_unique($times);

        sort($times);

        $until_date = date('Ymd', $this->get_end_range());
        foreach ($times as $time)
        {
        	if (! isset($time) || $time == '')
                continue;
            $date = date("Ymd", $time);
            $time = strtotime("$date 12:00:00");
            //$length = $this->get_end_date() - $this->get_start_date();
            //$end_date = $time + $length;


            /*if (date("Ymd", $time) != $this->get_start_date(false))
            {
            	$time = $time + $this->day_offset * (24 * 60 * 60);
            }*/

            if (isset($time) && $time != '' && ! in_array($time, $this->occurence_times) && $time <= $this->get_until() && $time >= $this->get_start_date(true, true) && $date <= $until_date)
            {
            	$this->set_count($this->get_count() - 1);
                if ((($time >= $view_start && $time <= $view_end) /*|| ($time < $view_start && $end_date > $view_start)*/) && $this->get_count() >= 0)
                {

                    $this->add_occurence_times($time);
                }
            }
        }
    }

    function set_correct_time($time)
    {
        $day = date('d', $time);
        $month = date('m', $time);
        $year = date('Y', $time);
        $hour = date('H', $this->get_start_date());
        $minute = date('i', $this->get_start_date());
        $second = date('s', $this->get_start_date());
        return mktime($hour, $minute, $second, $month, $day, $year);

    }

    function expand_bymonth($time, $year)
    {
        $bymonth = $this->get_bymonth();
        $byweekno = $this->get_byweekno();
        $bymonthday = $this->get_bymonthday();
        if (! empty($byweekno))
            return $time;
        if (empty($bymonth))
            $bymonth = array(date("n", $this->get_start_date()));
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
                {
                    $monthday = date("t", $time) + $monthday + 1;
                }
                $new_times[] = mktime(12, 0, 0, $month, $monthday, $year);
            }
        }
        return $new_times;
    }

    function date_of_week($time, $day)
    {
        $week_start_day = 'Monday';
        $timestamp = strtotime($time);

        $num = date('w', strtotime($week_start_day));
        $start_day_time = strtotime((date('w', $timestamp) == $num ? "$week_start_day" : "last $week_start_day"), $timestamp);

        $time = strtotime($day, $start_day_time);
        $time += (12 * 60 * 60);
        return date('Ymd', $time);
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
        $the_sunday = $this->date_of_week(date('Ymd', $time), $this->get_wkst(true));

        foreach ($byday as $key => $day)
        {
            ereg('([-\+]{0,1})?([0-9]+)?([A-Z]{2})', $day, $byday_arr);
            $on_day = $this->convert_day_name($byday_arr[3]);
            switch ($freq_type)
            {
                case self :: REPEAT_TYPE_WEEK :
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
        	if (in_array(date("n", $time), $bymonth))
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
        {
            if (in_array(strtolower(date("D", $time)), $byday3))
            {
                $new_times[] = $time;
            }
        }
        return $new_times;
    }

    function restrict_bysetpos($times, $freq = '')
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
