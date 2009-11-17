<?php
/**
 * $Id: ieee_lom_time.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.metadata.ieee_lom
 */
/**
 * A DateTime field used in IEEE LOM. This object contains a date & time value
 * and a description
 */
abstract class IeeeLomTime
{
    const DATETIME_FORMAT = 'Y-m-d\TH:i:sO';
    
    /**
     * The date & time value
     */
    //private $timestamp;
    

    protected $day;
    protected $month;
    protected $year;
    protected $hour;
    protected $min;
    protected $sec;
    
    /**
     * The description
     */
    protected $description;

    /**
     * Constructor
     * 
     * @param timestamp $datetime
     * @param LangString $description
     */
    public function IeeeLomTime($timestamp = null, $description = null)
    {
        $this->set_timestamp($timestamp);
        $this->set_description($description);
    }

    /**
     * Gets the date & time value
     * 
     * @return timestamp
     */
    public function get_timestamp()
    {
        return mktime($this->get_hour(), $this->get_min(), $this->get_sec(), $this->get_month(), $this->get_day(), $this->get_year());
    }

    /**
     * Return an ISO 8601 formatted duration
     *
     * @return string Formatted datetime
     */
    public function get_formatted_duration()
    {
        $duration_str = '';
        
        if (isset($this->year) || isset($this->month) || isset($this->day))
        {
            $duration_str .= 'P';
        }
        
        if (isset($this->year))
        {
            $duration_str .= $this->year . 'Y';
        }
        
        if (isset($this->month))
        {
            $duration_str .= $this->month . 'M';
        }
        
        if (isset($this->day))
        {
            $duration_str .= $this->day . 'D';
        }
        
        if (strlen($duration_str) > 0 && (isset($this->hour) || isset($this->min) || isset($this->sec)))
        {
            $duration_str .= 'T';
        }
        
        if (isset($this->hour))
        {
            $duration_str .= $this->hour . 'H';
        }
        
        if (isset($this->min))
        {
            $duration_str .= $this->min . 'M';
        }
        
        if (isset($this->sec))
        {
            $duration_str .= $this->sec . 'S';
        }
        
        return $duration_str;
    }

    public function get_day($with_leading_zero = false)
    {
        if ($with_leading_zero && isset($this->day))
        {
            return sprintf('%02d', $this->day);
        }
        else
        {
            return $this->day;
        }
    }

    public function get_month($with_leading_zero = false)
    {
        if ($with_leading_zero && isset($this->month))
        {
            return sprintf('%02d', $this->month);
        }
        else
        {
            return $this->month;
        }
    }

    public function get_year()
    {
        return $this->year;
    }

    public function get_hour($with_leading_zero = false)
    {
        if ($with_leading_zero && isset($this->hour))
        {
            return sprintf('%02d', $this->hour);
        }
        else
        {
            return $this->hour;
        }
    }

    public function get_min($with_leading_zero = false)
    {
        if ($with_leading_zero && isset($this->min))
        {
            return sprintf('%02d', $this->min);
        }
        else
        {
            return $this->min;
        }
    }

    public function get_sec($with_leading_zero = false)
    {
        if ($with_leading_zero && isset($this->sec))
        {
            return sprintf('%02d', $this->sec);
        }
        else
        {
            return $this->sec;
        }
    }

    /**
     * Set the timestamp of the IeeeLomDateTime instance
     *
     * @param timestamp $timestamp
     */
    public function set_timestamp($timestamp)
    {
        if (isset($timestamp) && is_numeric($timestamp))
        {
            $this->day = date('j', $timestamp);
            $this->month = date('n', $timestamp);
            $this->year = date('Y', $timestamp);
            $this->hour = date('G', $timestamp);
            $this->min = date('i', $timestamp);
            $this->sec = date('s', $timestamp);
            
            if ($this->hour == 0 && $this->min == 0 && $this->sec == 0)
            {
                unset($this->hour);
                unset($this->min);
                unset($this->sec);
            }
        }
    }

    public function set_day($day)
    {
        if (is_numeric($day))
        {
            $this->day = $day;
        }
    }

    public function set_month($month)
    {
        if (is_numeric($month))
        {
            $this->month = $month;
        }
    }

    public function set_year($year)
    {
        if (is_numeric($year))
        {
            $this->year = $year;
        }
    }

    public function set_hour($hour)
    {
        if (is_numeric($hour))
        {
            $this->hour = $hour;
        }
    }

    public function set_min($min)
    {
        if (is_numeric($min))
        {
            $this->min = $min;
        }
    }

    public function set_sec($sec)
    {
        if (is_numeric($sec))
        {
            $this->sec = $sec;
        }
    }

    /**
     * Gets the description
     * 
     * @return LangString
     */
    function get_description()
    {
        return $this->description;
    }

    function set_description($description)
    {
        $this->description = $description;
    }

}
?>