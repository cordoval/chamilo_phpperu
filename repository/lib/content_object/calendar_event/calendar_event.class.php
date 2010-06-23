<?php
/**
 * $Id: calendar_event.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.calendar_event
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
/**
 * This class represents a calendar event
 */
class CalendarEvent extends ContentObject implements Versionable, AttachmentSupport
{
	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

    /**
     * The start date of the calendar event
     */
    const PROPERTY_START_DATE = 'start_date';
    /**
     * The end date of the calendar event
     */
    const PROPERTY_END_DATE = 'end_date';
    /**
     * Wheter the event is to be repeated and
     * if so, when it should be repeated
     */
    const PROPERTY_REPEAT_TYPE = 'repeat_type';
    /**
     * The end date of the repetition
     */
    const PROPERTY_REPEAT_TO = 'repeat_to';

    /**
     * The different repetition types
     */
    const REPEAT_TYPE_NONE = '0';
    const REPEAT_TYPE_DAY = '1';
    const REPEAT_TYPE_WEEK = '2';
    const REPEAT_TYPE_WEEKDAYS = '3';
    const REPEAT_TYPE_BIWEEK = '4';
    const REPEAT_TYPE_MONTH = '5';
    const REPEAT_TYPE_YEAR = '6';

    /**
     * Gets the start date of this calendar event
     * @return int The start date
     */
    function get_start_date()
    {
        return $this->get_additional_property(self :: PROPERTY_START_DATE);
    }

    /**
     * Sets the start date of this calendar event
     * @param int The start date
     */
    function set_start_date($start_date)
    {
        return $this->set_additional_property(self :: PROPERTY_START_DATE, $start_date);
    }

    /**
     * Gets the end date of this calendar event
     * @return int The end date
     */
    function get_end_date()
    {
        return $this->get_additional_property(self :: PROPERTY_END_DATE);
    }

    /**
     * Sets the end date of this calendar event
     * @param int The end date
     */
    function set_end_date($end_date)
    {
        return $this->set_additional_property(self :: PROPERTY_END_DATE, $end_date);
    }

    /**
     * Gets the repeat-type of this calendar event
     * @return int The repeat-type
     */
    function get_repeat_type()
    {
        return $this->get_additional_property(self :: PROPERTY_REPEAT_TYPE);
    }

    /**
     * Sets the repeat-type of this calendar event
     * @param int The repeat-type
     */
    function set_repeat_type($repeat_type)
    {
        return $this->set_additional_property(self :: PROPERTY_REPEAT_TYPE, $repeat_type);
    }

    /**
     * Gets the end date of this calendar event repetition
     * @return int The repetition end date
     */
    function get_repeat_to()
    {
        return $this->get_additional_property(self :: PROPERTY_REPEAT_TO);
    }

    /**
     * Sets the end date of this calendar event repetition
     * @param int The repetition end date
     */
    function set_repeat_to($repeat_to)
    {
        return $this->set_additional_property(self :: PROPERTY_REPEAT_TO, $repeat_to);
    }

    /**
     * Returns whether or not the calendar event repeats itself
     * @return boolean
     */
    function repeats()
    {
        $repeat = $this->get_repeat_type();
        return ($repeat != '0');
    }

    /**
     * Returns whether or not the calendar event repeats itself indefinately
     * @return boolean
     */
    function repeats_indefinately()
    {
        $repeat_to = $this->get_repeat_to();
        return ($repeat_to == '0');
    }

    /**
     * Return the repeat-type as a string
     */
    function get_repeat_as_string()
    {
        $repeat = $this->get_repeat_type();

        switch ($repeat)
        {
            case self :: REPEAT_TYPE_DAY :
                $string = Translation :: get('Daily');
                break;
            case self :: REPEAT_TYPE_WEEK :
                $string = Translation :: get('Weekly');
                break;
            case self :: REPEAT_TYPE_WEEKDAYS :
                $string = Translation :: get('Weekdays');
                break;
            case self :: REPEAT_TYPE_BIWEEK :
                $string = Translation :: get('BiWeekly');
                break;
            case self :: REPEAT_TYPE_MONTH :
                $string = Translation :: get('Monthly');
                break;
            case self :: REPEAT_TYPE_YEAR :
                $string = Translation :: get('Yearly');
                break;
        }

        return $string;
    }

    function get_repeats($from_date = 0, $to_date = 0)
    {
        if ($from_date == 0)
        {
            $from_date = mktime(0, 0, 0, date('n'), 1, date('Y'));
        }

        if ($to_date == 0)
        {
            $to_date = strtotime('+ 1 year', $from_date);
        }

        $repeats = array();

        $repeat_start_date = $this->get_start_date();
        $repeat_end_date = $this->get_end_date();

        //echo 'Original start date : ' . date('r', $repeat_start_date) . '<br />';


        while ($repeat_start_date <= $from_date && ($repeat_end_date <= $from_date))
        {
            $repeat_start_date = $this->get_next_repeat_date($repeat_start_date);
            $repeat_end_date = $this->get_next_repeat_date($repeat_end_date);
        }

        //echo 'First occurence in calendar : ' . date('r', $repeat_start_date) . '<br />';


        $repeat_until = $this->get_repeat_to();
        //echo 'Until : ' . date('r', $repeat_until) . '<br />';


        //echo 'Calendar End : ' . date('r', $to_date) . '<br /><br />';


        while ($repeat_start_date <= $to_date && ($repeat_start_date <= $repeat_until || $this->repeats_indefinately()))
        {
            //echo 'Repeat : ' . date('r', $repeat_start_date) . '<br />';
            $the_clone = clone $this;
            $the_clone->set_start_date($repeat_start_date);
            $the_clone->set_end_date($repeat_end_date);

            $repeats[] = $the_clone;

            $repeat_start_date = $this->get_next_repeat_date($repeat_start_date);
            $repeat_end_date = $this->get_next_repeat_date($repeat_end_date);
        }
        //echo '<br /><br /><br />';


        return $repeats;
    }

    function get_next_repeat_date($date)
    {
        $repeat = $this->get_repeat_type();

        switch ($repeat)
        {
            case self :: REPEAT_TYPE_DAY :
                $date = strtotime("+1 day", $date);
                break;
            case self :: REPEAT_TYPE_WEEK :
                $date = strtotime("+1 week", $date);
                break;
            case self :: REPEAT_TYPE_WEEKDAYS :
                $day = date('N', $date);

                switch ($day)
                {
                    case 5 :
                        $date = strtotime("+3 day", $date);
                        break;
                    case 6 :
                        $date = strtotime("+2 day", $date);
                        break;
                    default :
                        $date = strtotime("+1 day", $date);
                        break;
                }
                break;
            case self :: REPEAT_TYPE_BIWEEK :
                $date = strtotime("+2 week", $date);
                break;
            case self :: REPEAT_TYPE_MONTH :
                $date = strtotime("+1 month", $date);
                break;
            case self :: REPEAT_TYPE_YEAR :
                $date = strtotime("+1 year", $date);
                break;
        }

        return $date;
    }

    static function get_repeat_options()
    {
        $options = array();

        $options[self :: REPEAT_TYPE_DAY] = Translation :: get('Daily');
        $options[self :: REPEAT_TYPE_WEEK] = Translation :: get('Weekly');
        $options[self :: REPEAT_TYPE_WEEKDAYS] = Translation :: get('Weekdays');
        $options[self :: REPEAT_TYPE_BIWEEK] = Translation :: get('BiWeekly');
        $options[self :: REPEAT_TYPE_MONTH] = Translation :: get('Monthly');
        $options[self :: REPEAT_TYPE_YEAR] = Translation :: get('Yearly');

        return $options;
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_START_DATE, self :: PROPERTY_END_DATE, self :: PROPERTY_REPEAT_TYPE, self :: PROPERTY_REPEAT_TO);
    }

    function get_icon_name()
    {
        if ($this->repeats())
        {
            return $this->get_type() . '_repeat';
        }
        else
        {
            return $this->get_type();
        }
    }
}
?>