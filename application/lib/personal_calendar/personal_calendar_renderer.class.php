<?php
/**
 * $Id: personal_calendar_renderer.class.php 127 2009-11-09 13:11:56Z vanpouckesven $
 * @package application.personal_calendar
 */
/**
 * A renderer to display a personal calendar to the end user
 */
abstract class PersonalCalendarRenderer
{
    /**
     * The personal calendar of which the events will be displayed
     */
    private $personal_calendar;
    /**
     * The time of the moment to render
     */
    private $display_time;
    /**
     *
     */
    private $legend;

    /**
     * Constructor
     * @param PersonalCalendar $personal_calendar
     * @param int $display_time
     */
    function PersonalCalendarRenderer($personal_calendar, $display_time)
    {
        $this->personal_calendar = $personal_calendar;
        $this->display_time = $display_time;
        $this->legend = array();
    }

    /**
     * Gets the evenst to display
     * @see PersonalCalendarManager :: get_events
     * @param int $from_date
     * @param int $to_date
     */
    public function get_events($from_date, $to_date)
    {
    	return $this->personal_calendar->get_events($from_date, $to_date);
    }

    /**
     * Gets the time
     * @return int
     */
    public function get_time()
    {
        return $this->display_time;
    }

    /**
     * Gets the personal calendar object in which this renderer is used
     * @return PersonalCalendar
     */
    public function get_parent()
    {
        return $this->personal_calendar;
    }

    /**
     * @see PersonalCalendarManager :: get_url()
     */
    public function get_url($parameters = array (), $encode = false, $filter = false, $filterOn = array())
    {
        return $this->personal_calendar->get_url($parameters, $encode, $filter, $filterOn);
    }

    /**
     * Renders the calendar
     * @return string A html representation of the events in this calendar.
     */
    abstract function render();

    /**
     * Retrieves a color
     * @param mixed $key The key from which the color will be created. Calling
     * this function with the same key returns the same color.
     * @return string A color string which can be used as a value in CSS rules.
     */
    public function get_color($key = null, $fade = false)
    {
        if (is_null($key))
        {
            $this->legend[Translation :: get('MyAgenda')] = 'red';
            return 'red';
        }
        if (! isset($this->legend[$key]))
        {
            $color_number = substr(ereg_replace('[0a-zA-Z]', '', md5(serialize($key))), 0, 9);
            $rgb = array();
            $rgb['r'] = substr($color_number, 0, 3) % 255;
            $rgb['g'] = substr($color_number, 2, 3) % 255;
            $rgb['b'] = substr($color_number, 4, 3) % 255;
            
            $rgb['fr'] = round(($rgb['r'] + 234) / 2);
            $rgb['fg'] = round(($rgb['g'] + 234) / 2);
            $rgb['fb'] = round(($rgb['b'] + 234) / 2);
            
            $this->legend[$key]['full'] = 'rgb(' . $rgb['r'] . ',' . $rgb['g'] . ',' . $rgb['b'] . ')';
            $this->legend[$key]['fade'] = 'rgb(' . $rgb['fr'] . ',' . $rgb['fg'] . ',' . $rgb['fb'] . ')';
        }
        
        if ($fade)
        {
            return $this->legend[$key]['fade'];
        }
        else
        {
            return $this->legend[$key]['full'];
        }
    }

    /**
     * Builds a color-based legend for the personal calendar to help users to
     * see which applications and locations are the origin of the the published
     * events
     * @return string
     */
    public function build_legend()
    {
        $result = '<div style="margin-top: 10px; clear: both;">';
        foreach ($this->legend as $key => $colors)
        {
            $result .= '<span style="display:block; float: left; margin-right: 2px; width: 10px; height: 10px; border: 1px solid black; background-color: ' . $colors['full'] . '">&nbsp;</span><span style="float:left; margin-right: 15px;">' . $key . '</span>';
        }
        $result .= '</div>';
        return $result;
    }
}
?>