<?php
namespace common\libraries;

use DateTimeZone;
use DateTime;

/**
 * $Id: datetime_utilities.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.datetime
 */
class DatetimeUtilities
{
    
    /**
     * Get a four digit year from a two digit year.
     * The century depends on the year difference between the given year and the current year.
     *
     * e.g. with default $years_difference_for_century value of 20:
     * 		- calling the function in 2009 with a given $year value of 19 return 2019
     * 		- calling the function in 2009 with a given $year value of 75 return 1975
     *
     * @param $years_difference_for_century The maximum difference of years between the current year and the given year to return the current century
     * @return integer A year number
     */
    public static function get_complete_year($year, $years_difference_for_century = 20)
    {
        if (is_numeric($year))
        {
            if ($year > 100)
            {
                return $year;
            }
            else
            {
                if ($year <= date('y') || $year - date('y') < $years_difference_for_century)
                {
                    return (date('Y') - date('y') + $year);
                }
                else
                {
                    return (date('Y') - date('y') - 100 + $year);
                }
            }
        }
        else
        {
            return null;
        }
    }

	/**
     * formats the date according to the locale settings
     *
     * @author  Patrick Cool <patrick.cool@UGent.be>, Ghent University
     * @author  Christophe Gesch� <gesche@ipm.ucl.ac.be>
     *          originally inspired from from PhpMyAdmin
     * @param  string  $formatOfDate date pattern
     * @param  integer $timestamp, default is NOW.
     * @return the formatted date
     */
    public static function format_locale_date($dateFormat = null, $timeStamp = -1)
    {
    	if(!$dateFormat)
    	{
            $dateFormat = self :: default_date_time_format();
    	}

        $DaysShort = self::get_days_short(); // Defining the shorts for the days
        $DaysLong = self::get_days_long();  // Defining the days of the week to allow translation of the days
        $MonthsShort = self::get_month_short(); // Defining the shorts for the months
        $MonthsLong = self::get_month_long(); // Defining the months of the year to allow translation of the months

        // with the ereg  we  replace %aAbB of date format
        //(they can be done by the system when locale date aren't aivailable

        $date = preg_replace('/%[A]/', $DaysLong[(int) strftime('%w', $timeStamp)], $dateFormat);
        $date = preg_replace('/%[a]/', $DaysShort[(int) strftime('%w', $timeStamp)], $date);
        $date = preg_replace('/%[B]/', $MonthsLong[(int) strftime('%m', $timeStamp) - 1], $date);
        $date = preg_replace('/%[b]/', $MonthsShort[(int) strftime('%m', $timeStamp) - 1], $date);

        if ($timeStamp == - 1)
        {
            $timeStamp = time();
        }

        return strftime($date, $timeStamp);
    }
    
    private static function default_date_time_format()
    {
        $translator = Translation :: get_instance();
        $short_date = $translator->get('DateFormatShort');
        $time = $translator->get('TimeNoSecFormat');
        $dateFormat = "{$short_date},  {$time}";
        return $dateFormat;
    }

    /**
     * Convert the given date to the selected timezone
     * @param String $date The date
     * @param String $timezone The selected timezone
     */
    public static function convert_date_to_timezone($date, $format = null, $timezone = null)
    {
        if(!$dateFormat)
    	{
            $dateFormat = self :: default_date_time_format();
    	}

    	if(!$timezone)
    	{
    		$timezone = LocalSetting :: get('platform_timezone');
    		if(!$timezone)
    		{
    			return self :: format_locale_date($format, $date);
    		}
    	}

    	$date_time_zone = new DateTimeZone($timezone);
    	$gmt_time_zone = new DateTimeZone('GMT');

        $date_time = new DateTime($date, $gmt_time_zone);
        $offset = $date_time_zone->getOffset($date_time);

        return self :: format_locale_date($format, $date_time->format('U') + $offset);
    }



 	/**
     * Convert the seconds to h:m:s or m:s or s
     * @param String $time
     */
    static function convert_seconds_to_hours($time)
    {
        if ($time / 3600 < 1 && $time / 60 < 1)
        {
            $converted_time = $time . 's';
        }
        else
        {
            if ($time / 3600 < 1)
            {
                $min = (int) ($time / 60);
                $sec = $time % 60;
                $converted_time = $min . 'm ' . $sec . 's';
            }
            else
            {
                $hour = (int) ($time / 3600);
                $rest = $time % 3600;
                $min = (int) ($rest / 60);
                $sec = $rest % 60;
                $converted_time = $hour . 'h ' . $min . 'm ' . $sec . 's';
            }
        }
        return $converted_time;
    }

    /**
     * Defining the shorts for the days. Memoized.
     *
     * @return array
     */
    public static function get_days_short(){
    	static $result = false;
    	if($result){
    		return $result;
    	}
        
        $translator = Translation :: get_instance();

        return $result = array(
            $translator->get("SundayShort"), 
            $translator->get("MondayShort"), 
            $translator->get("TuesdayShort"), 
            $translator->get("WednesdayShort"), 
            $translator->get("ThursdayShort"), 
            $translator->get("FridayShort"), 
            $translator->get("SaturdayShort")
        );
    }

    /**
     * Defining the days of the week to allow translation of the days. Memoized.
     *
     * @return array
     */
    public static function get_days_long(){
    	static $result = false;
    	if($result){
    		return $result;
    	}
        $translator = Translation :: get_instance();
        
        return $result = array(
            $translator->get("SundayLong"), 
            $translator->get("MondayLong"), 
            $translator->get("TuesdayLong"), 
            $translator->get("WednesdayLong"), 
            $translator->get("ThursdayLong"), 
            $translator->get("FridayLong"), 
            $translator->get("SaturdayLong")
        );
    }

	/**
     * Defining the shorts for the months. Memoized.
     *
     * @return array
     */
    public static function get_month_short(){
    	static $result = false;
    	if($result){
    		return $result;
    	}
        
        $translator = Translation :: get_instance();
        
        return $result = array(
            $translator->get("JanuaryShort"), 
            $translator->get("FebruaryShort"), 
            $translator->get("MarchShort"), 
            $translator->get("AprilShort"), 
            $translator->get("MayShort"), 
            $translator->get("JuneShort"), 
            $translator->get("JulyShort"),
            $translator->get("AugustShort"),
            $translator->get("SeptemberShort"),
            $translator->get("OctoberShort"),
            $translator->get("NovemberShort"),
            $translator->get("DecemberShort"),
            
        );

    }

	/**
     * Defining the shorts for the months. Memoized.
     *
     * @return array
     */
    public static function get_month_long(){
    	static $result = false;
    	if($result){
    		return $result;
    	}
        
        $translator = Translation :: get_instance();
        
        return $result = array(
            $translator->get("JanuaryLong"), 
            $translator->get("FebruaryLong"), 
            $translator->get("Marchv"), 
            $translator->get("AprilLong"), 
            $translator->get("MayLong"), 
            $translator->get("JuneLong"), 
            $translator->get("JulyLong"),
            $translator->get("AugustLong"),
            $translator->get("SeptemberLong"),
            $translator->get("OctoberLong"),
            $translator->get("NovemberLong"),
            $translator->get("DecemberLong"),
            
        );

    }





















}
?>