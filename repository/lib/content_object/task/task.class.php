<?php
/**
 * $Id: task.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.task
 */
/**
 * This class represents a task
 */
class Task extends ContentObject implements Versionable
{
	/**
     * The start date of the calendar event
     */
    const PROPERTY_START_DATE = 'start_date';
    /**
     * The end date of the calendar event
     */
    const PROPERTY_END_DATE = 'end_date';
     /**
     * The type of the task
     */
    const PROPERTY_TASK_TYPE = 'task_type';
     /**
     * The priority of the task
     */
    const PROPERTY_TASK_PRIORITY = 'priority';
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

    /*
     * The different types of task
     */
    const TYPE_ANNIVERSARY = '0';
    const TYPE_BUSINESS = '1';
    const TYPE_CALL = '2';
    const TYPE_HOLIDAY = '3';
    const TYPE_GIFT = '4';
    const TYPE_CLIENT = '5';
    const TYPE_COMPETITION = '6';
    const TYPE_CONFERENCE = '7';
    const TYPE_VARIOUS = '8';
    const TYPE_SUPPLIER = '9';
    const TYPE_IDEAS = '10';
    const TYPE_PUBLIC_HOLIDAY = '11';
    const TYPE_PRIVATE = '12';
    const TYPE_FAVORITE = '13';
    const TYPE_PROBLEMS = '14';
    const TYPE_PROFESSIONAL = '15';
    const TYPE_PROJECTS = '16';
    const TYPE_MEETING = '17';
    const TYPE_MONITORING = '18';
    const TYPE_TRAVEL = '19';

    const PRIORITY_LOW = '0';
    const PRIORITY_NORMAL = '1';
    const PRIORITY_HIGH = '2';

	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

	/**
 	* Gets the type of this task
 	* @return int task type
 	*/
    function get_task_type()
    {
    	return $this->get_additional_property(self :: PROPERTY_TASK_TYPE);
    }

    /**
     * Sets the type of this task
     * @param int The type
     */
    function set_task_type($task_type)
    {
    	return $this->set_additional_property(self :: PROPERTY_TASK_TYPE, $task_type);
    }

   /**
 	* Gets the priority of this task
 	* @return String task priority
 	*/
    function get_task_priority()
    {
    	return $this->get_additional_property(self :: PROPERTY_TASK_PRIORITY);
    }

    /**
     * Sets the priority of this task
     * @param String The priority
     */
    function set_task_priority($task_priority)
    {
    	return $this->set_additional_property(self :: PROPERTY_TASK_PRIORITY, $task_priority);
    }

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

    /**
     * Attachments are supported by calendar events
     * @return boolean Always true
     */
    function supports_attachments()
    {
        return true;
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_TASK_TYPE, self :: PROPERTY_TASK_PRIORITY, self :: PROPERTY_START_DATE, self :: PROPERTY_END_DATE, self :: PROPERTY_REPEAT_TYPE, self :: PROPERTY_REPEAT_TO);
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

    static function get_priority_options()
    {
        $options = array();

        $options[self :: PRIORITY_LOW] = Translation :: get('Low');
        $options[self :: PRIORITY_NORMAL] = Translation :: get('Normal');
        $options[self :: PRIORITY_HIGH] = Translation :: get('High');

        return $options;
    }

	static function get_types_options()
    {
        $types = array();

        $types[self :: TYPE_ANNIVERSARY] = Translation :: get('Anniversary');
        $types[self :: TYPE_BUSINESS] = Translation :: get('Business');
        $types[self :: TYPE_CALL] = Translation :: get('Call');
        $types[self :: TYPE_HOLIDAY] = Translation :: get('Holiday');
        $types[self :: TYPE_GIFT] = Translation :: get('Gift');
        $types[self :: TYPE_CLIENT] = Translation :: get('Client');
        $types[self :: TYPE_COMPETITION] = Translation :: get('Competition');
        $types[self :: TYPE_CONFERENCE] = Translation :: get('Conference');
        $types[self :: TYPE_VARIOUS] = Translation :: get('Various');
        $types[self :: TYPE_SUPPLIER] = Translation :: get('Supplier');
        $types[self :: TYPE_IDEAS] = Translation :: get('Ideas');
        $types[self :: TYPE_PUBLIC_HOLIDAY] = Translation :: get('PublicHoliday');
        $types[self :: TYPE_PRIVATE] = Translation :: get('Private');
        $types[self :: TYPE_FAVORITE] = Translation :: get('Favorite');
        $types[self :: TYPE_PROBLEMS] = Translation :: get('Problems');
        $types[self :: TYPE_PROFESSIONAL] = Translation :: get('Professional');
        $types[self :: TYPE_PROJECTS] = Translation :: get('Projects');
        $types[self :: TYPE_MEETING] = Translation :: get('Meeting');
        $types[self :: TYPE_MONITORING] = Translation :: get('Monitoring');
        $types[self :: TYPE_TRAVEL] = Translation :: get('Travel');
        asort($types);
        return $types;
    }

    /**
     * Return the task-priority as a string
     */
    function get_task_priority_as_string()
    {
        $priority = $this->get_task_priority();

        switch ($priority)
        {
            case self :: PRIORITY_LOW :
                $string = Translation :: get('Low');
                break;
            case self :: PRIORITY_NORMAL :
                $string = Translation :: get('Normal');
                break;
            case self :: PRIORITY_HIGH :
                $string = Translation :: get('High');
                break;
        }
        return $string;
    }

	/**
     * Return the task-type as a string
     */
    function get_task_type_as_string()
    {
        $type = $this->get_task_type();

        switch ($type)
        {
            case self :: TYPE_ANNIVERSARY :
                $string = Translation :: get('Anniversary');
                break;
            case self :: TYPE_BUSINESS :
                $string = Translation :: get('Business');
                break;
            case self :: TYPE_CALL :
                $string = Translation :: get('Call');
                break;
            case self :: TYPE_HOLIDAY :
                $string = Translation :: get('Holiday');
                break;
            case self :: TYPE_GIFT :
                $string = Translation :: get('Gift');
                break;
            case self :: TYPE_CLIENT :
                $string = Translation :: get('Client');
                break;
            case self :: TYPE_COMPETITION :
                $string = Translation :: get('Competition');
                break;
            case self :: TYPE_CONFERENCE :
                $string = Translation :: get('Conference');
                break;
            case self :: TYPE_VARIOUS :
                $string = Translation :: get('Various');
                break;
            case self :: TYPE_SUPPLIER :
                $string = Translation :: get('Supplier');
                break;
            case self :: TYPE_IDEAS :
                $string = Translation :: get('Ideas');
                break;
             case self :: TYPE_PUBLIC_HOLIDAY :
                $string = Translation :: get('PublicHoliday');
                break;
             case self :: TYPE_PRIVATE :
                $string = Translation :: get('Private');
                break;
            case self :: TYPE_FAVORITE :
                $string = Translation :: get('Favorite');
                break;
            case self :: TYPE_PROBLEMS :
                $string = Translation :: get('Problems');
                break;
            case self :: TYPE_PROFESSIONAL :
                $string = Translation :: get('Professional');
                break;
            case self :: TYPE_PROJECTS :
                $string = Translation :: get('Projects');
                break;
            case self :: TYPE_MEETING :
                $string = Translation :: get('Meeting');
                break;
            case self :: TYPE_MONITORING :
                $string = Translation :: get('Monitoring');
                break;
            case self :: TYPE_TRAVEL :
                $string = Translation :: get('Travel');
                break;
        }
        return $string;
    }
}
?>