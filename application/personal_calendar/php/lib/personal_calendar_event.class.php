<?php
/**
 * $Id: personal_calendar_event.class.php 127 2009-11-09 13:11:56Z vanpouckesven $
 * @package application.personal_calendar
 */
/**
 * A personcal calendar events bundles a learning object (CalendarEvent) and a
 * user in the application.
 */
class PersonalCalendarEvent
{
    private $start_date;
    private $end_date;
    private $url;
    private $title;
    private $content;
    private $source;
    private $id;

    function PersonalCalendarEvent()
    {
    }

    function set_id($id)
    {
        $this->id = $id;
    }

    function get_id()
    {
        return $this->id;
    }

    function get_start_date()
    {
        return $this->start_date;
    }

    function get_end_date()
    {
        return $this->end_date;
    }

    function get_url()
    {
        return $this->url;
    }

    function get_title()
    {
        return $this->title;
    }

    function get_content()
    {
        return $this->content;
    }

    function get_source()
    {
        return $this->source;
    }

    function set_start_date($start_date)
    {
        $this->start_date = $start_date;
    }

    function set_end_date($end_date)
    {
        $this->end_date = $end_date;
    }

    function set_url($url)
    {
        $this->url = $url;
    }

    function set_title($title)
    {
        $this->title = $title;
    }

    function set_content($content)
    {
        $this->content = $content;
    }

    function set_source($source)
    {
        $this->source = $source;
    }
}
?>