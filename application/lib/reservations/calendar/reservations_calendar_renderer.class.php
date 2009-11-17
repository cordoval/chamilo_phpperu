<?php
/**
 * $Id: reservations_calendar_renderer.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.calendar
 */
/**
 * A renderer to display a personal calendar to the end user
 */
abstract class ReservationsCalendarRenderer
{
    /**
     * The personal calendar of which the events will be displayed
     */
    private $browser;
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
     * @param ReservationsCalendar $browser
     * @param int $display_time
     */
    function ReservationsCalendarRenderer($browser, $display_time)
    {
        $this->browser = $browser;
        $this->display_time = $display_time;
        $this->legend = array(Translation :: get('OpenReservation') => 'lime', Translation :: get('Timepicker') => 'navy', Translation :: get('TimepickerToSmall') => 'yellow', Translation :: get('Outofperiod') => 'orange', Translation :: get('Reserved') => 'red', Translation :: get('Blackout') => 'black', Translation :: get('Now') => '#FF9999');
    }

    /**
     * Gets the evenst to display
     * @see ReservationsCalendar::get_events
     * @param int $from_date
     * @param int $to_date
     */
    public function get_events($from_date, $to_date)
    {
        return $this->browser->get_events($from_date, $to_date);
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
     * @return ReservationsCalendar
     */
    public function get_parent()
    {
        return $this->browser;
    }

    /**
     * @see ReservationsCalendar::get_url()
     */
    public function get_url($parameters = array (), $encode = false, $filter = false, $filterOn = array())
    {
        return $this->browser->get_url($parameters, $encode, $filter, $filterOn);
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
    public function get_color($key = null)
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
            $this->legend[$key] = 'rgb(' . $rgb['r'] . ',' . $rgb['g'] . ',' . $rgb['b'] . ')';
        }
        return $this->legend[$key];
    }

    /**
     * Builds a color-based legend for the personal calendar to help users to
     * see which applications and locations are the origin of the the published
     * events
     * @return string
     */
    public function build_legend()
    {
        $result = '<div style="margin-top: 10px; float: right;">';
        foreach ($this->legend as $key => $color)
        {
            $result .= '<span style="display:block; float: left; margin-right: 2px; width: 10px; height: 10px; border: 1px solid black; background-color: ' . $color . '">&nbsp;</span><span style="float:left; margin-right: 15px;">' . $key . '</span>';
        }
        $result .= '</div>';
        return $result;
    }
}
?>