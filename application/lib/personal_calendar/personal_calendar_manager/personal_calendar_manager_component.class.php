<?php
/**
 * $Id: personal_calendar_manager_component.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.personal_calendar.personal_calendar_manager
 */

abstract class PersonalCalendarManagerComponent extends WebApplicationComponent
{

    /**
     * Constructor
     * @param PersonalCalendarManager $pcm The personal calendar manager which provides this component
     */
    protected function PersonalCalendarManagerComponent($pcm)
    {
        parent :: __construct($pcm);
    }

    function retrieve_calendar_event_publication($publication_id)
    {
        return $this->get_parent()->retrieve_calendar_event_publication($publication_id);
    }

    function get_events($from_date, $to_date)
    {
        return $this->get_parent()->get_events($from_date, $to_date);
    }

    /**
     * @see CalendarManager :: get_search_condition()
     */
    function get_search_condition()
    {
        return $this->get_parent()->get_search_condition();
    }

    /**
     * @see CalendarManager :: get_publication_deleting_url()
     */
    function get_publication_deleting_url($publication)
    {
        return $this->get_parent()->get_publication_deleting_url($publication);
    }

    /**
     * @see CalendarManager :: get_publication_editing_url()
     */
    function get_publication_editing_url($publication)
    {
        return $this->get_parent()->get_publication_editing_url($publication);
    }

    /**
     * @see CalendarManager :: get_publication_viewing_url()
     */
    function get_publication_viewing_url($calendar)
    {
        return $this->get_parent()->get_publication_viewing_url($calendar);
    }

    function get_publication_viewing_link($calendar)
    {
        return $this->get_parent()->get_publication_viewing_link($calendar);
    }

    /**
     * @see CalendarManager :: get_calendar_creation_url()
     */
    function get_calendar_creation_url()
    {
        return $this->get_parent()->get_calendar_creation_url();
    }

    /**
     * @see CalendarManager :: get_publication_reply_url()
     */
    function get_publication_reply_url($calendar)
    {
        return $this->get_parent()->get_publication_reply_url($calendar);
    }

    function force_menu_url($url)
    {
        return $this->get_parent()->force_menu_url($url);
    }

    function get_publication_attachment_viewing_url($calendar)
    {
        return $this->get_parent()->get_publication_attachment_viewing_url($calendar);
    }

    function get_user_info($user_id)
    {
        return $this->get_parent()->get_user_info($user_id);
    }

    function get_ical_export_url($publication)
    {
        return $this->get_parent()->get_ical_export_url($publication);
    }

    function get_ical_import_url()
    {
        return $this->get_parent()->get_ical_import_url();
    }
}
?>