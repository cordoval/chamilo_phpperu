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

}
?>