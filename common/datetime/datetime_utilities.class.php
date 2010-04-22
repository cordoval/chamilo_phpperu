<?php

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
    public static function format_locale_date($dateFormat, $timeStamp = -1)
    {
    	if(!$dateFormat)
    	{
    		$dateFormat = Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat');
    	}
    	
    	// Defining the shorts for the days
        $DaysShort = array(Translation :: get("SundayShort"), Translation :: get("MondayShort"), Translation :: get("TuesdayShort"), Translation :: get("WednesdayShort"), Translation :: get("ThursdayShort"), Translation :: get("FridayShort"), Translation :: get("SaturdayShort"));
        // Defining the days of the week to allow translation of the days
        $DaysLong = array(Translation :: get("SundayLong"), Translation :: get("MondayLong"), Translation :: get("TuesdayLong"), Translation :: get("WednesdayLong"), Translation :: get("ThursdayLong"), Translation :: get("FridayLong"), Translation :: get("SaturdayLong"));
        // Defining the shorts for the months
        $MonthsShort = array(Translation :: get("JanuaryShort"), Translation :: get("FebruaryShort"), Translation :: get("MarchShort"), Translation :: get("AprilShort"), Translation :: get("MayShort"), Translation :: get("JuneShort"), Translation :: get("JulyShort"), Translation :: get("AugustShort"), Translation :: get("SeptemberShort"), Translation :: get("OctoberShort"), Translation :: get("NovemberShort"), Translation :: get("DecemberShort"));
        // Defining the months of the year to allow translation of the months
        $MonthsLong = array(Translation :: get("JanuaryLong"), Translation :: get("FebruaryLong"), Translation :: get("MarchLong"), Translation :: get("AprilLong"), Translation :: get("MayLong"), Translation :: get("JuneLong"), Translation :: get("JulyLong"), Translation :: get("AugustLong"), Translation :: get("SeptemberLong"), Translation :: get("OctoberLong"), Translation :: get("NovemberLong"), Translation :: get("DecemberLong"));

        // with the ereg  we  replace %aAbB of date format
        //(they can be done by the system when  locale date aren't aivailable

        $date = ereg_replace('%[A]', $DaysLong[(int) strftime('%w', $timeStamp)], $dateFormat);
        $date = ereg_replace('%[a]', $DaysShort[(int) strftime('%w', $timeStamp)], $date);
        $date = ereg_replace('%[B]', $MonthsLong[(int) strftime('%m', $timeStamp) - 1], $date);
        $date = ereg_replace('%[b]', $MonthsShort[(int) strftime('%m', $timeStamp) - 1], $date);

        if ($timeStamp == - 1)
            $timeStamp = time();
        
        return strftime($date, $timeStamp);
    }
    
    /**
     * Convert the given date to the selected timezone
     * @param String $date The date
     * @param String $timezone The selected timezone
     */
    public static function convert_date_to_timezone($date, $format = null, $timezone = null)
    {
   		if(!$format)
    	{
    		$format = Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat');
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
		$date_time = new DateTime($date, $date_time_zone);
 
		return self :: format_locale_date($format, $date_time->format('U'));
    }

}
?>