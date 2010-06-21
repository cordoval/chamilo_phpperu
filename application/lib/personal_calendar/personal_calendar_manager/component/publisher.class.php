<?php
/**
 * $Id: publisher.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.personal_calendar.personal_calendar_manager.component
 */
require_once dirname(__FILE__) . '/../personal_calendar_manager.class.php';
require_once dirname(__FILE__) . '/../../publisher/personal_calendar_publisher.class.php';

class PersonalCalendarManagerPublisherComponent extends PersonalCalendarManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR)), Translation :: get('PersonalCalendar')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Publish')));
        $trail->add_help('personal calender general');

        $repo_viewer = new RepoViewer($this, array(CalendarEvent :: get_type_name(), Task :: get_type_name(), 'external_calendar'));

        if (! $repo_viewer->is_ready_to_be_published())
        {
            $repo_viewer->run();
        }
        else
        {
            $publisher = new PersonalCalendarPublisher($this);
            $publisher->get_publications_form($repo_viewer->get_selected_objects());
        }
    }
}
?>