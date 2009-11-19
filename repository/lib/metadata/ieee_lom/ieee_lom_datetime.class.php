<?php
/**
 * $Id: ieee_lom_datetime.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.metadata.ieee_lom
 */
require_once (dirname(__FILE__) . '/ieee_lom_time.class.php');

class IeeeLomDateTime extends IeeeLomTime
{

    public function IeeeLomDateTime($timestamp = null, $description = null)
    {
        parent :: IeeeLomTime($timestamp, $description);
    }

    /**
     * Return an ISO 8601 formatted datetime
     *
     * @return string Formatted datetime
     */
    public function get_datetime()
    {
        $datetime_str = '';
        $time_str = '';
        
        if (isset($this->day) && isset($this->month) && isset($this->year))
        {
            $datetime_str = DatetimeUtilities :: get_complete_year($this->year) . '-' . $this->get_month(true) . '-' . $this->get_day(true);
        }
        
        $hour = $this->get_hour(true);
        $min = $this->get_min(true);
        $sec = $this->get_sec(true);
        
        if (isset($hour) || (isset($hour) && isset($min)) || (isset($hour) && isset($min) && isset($sec)))
        {
            if (isset($hour))
            {
                $time_str = 'T' . $hour;
            }
            else
            {
                $time_str = 'T00';
            }
            
            if (isset($min))
            {
                $time_str .= ':' . $min;
            }
            else
            {
                $time_str .= ':00';
            }
            
            if (isset($sec))
            {
                $time_str .= ':' . $sec;
            }
            else
            {
                $time_str .= ':00';
            }
        }
        
        if (strlen($time_str) > 0)
        {
            $datetime_str .= $time_str;
        }
        
        return $datetime_str;
    }

    /**
     * Set the the instance datetime value
     * 
     * @param $date_string string Date in format accepted by strtotime() 
     */
    public function set_datetime_from_string($date_string)
    {
        $this->set_timestamp(strtotime($date_string));
    }

}
?>