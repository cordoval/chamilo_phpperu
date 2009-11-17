<?php
/**
 * $Id: calendar_table.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common
 */
require_once ('HTML/Table.php');
/**
 * A tabular representation of a calendar
 */
abstract class CalendarTable extends HTML_Table
{
    /**
     * A time in the month represented by this calendar
     */
    private $display_time;
    /**
     * The list of events to show
     */
    private $events_to_show;

    /**
     *
     */
    public function CalendarTable($display_time)
    {
        if (is_null($display_time))
        {
            $display_time = time();
        }
        $this->display_time = $display_time;
        parent :: HTML_Table(array('class' => 'calendar_table'));
    }

    /**
     *
     */
    public function get_display_time()
    {
        return $this->display_time;
    }

    /**
     *
     */
    public function set_display_time($time)
    {
        $this->display_time = $time;
    }

    /**
     * Add an event to the calendar
     * @param int $time A time in the day on which the event should be displayed
     * @param string $content The html content to insert in the month calendar
     */
    public function add_event($time, $content)
    {
        $this->events_to_show[$time][] = $content;
    }

    /**
     * Gets the list of events to show sorted by their starting time
     * @return array
     */
    public function get_events_to_show()
    {
        ksort($this->events_to_show);
        return $this->events_to_show;
    }

    public function contains_events_for_time($time)
    {
        return count($this->events_to_show[$time]) > 0;
    }

    /**
     * Gets the first date which will be displayed by this calendar.
     * @return int
     */
    abstract function get_start_time();

    /**
     * Gets the end date which will be displayed by this calendar.
     * @return int
     */
    abstract function get_end_time();
}
?>