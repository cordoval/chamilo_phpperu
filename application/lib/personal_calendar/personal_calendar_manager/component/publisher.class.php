<?php
/**
 * $Id: publisher.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.personal_calendar.personal_calendar_manager.component
 */
require_once dirname(__FILE__) . '/../personal_calendar_manager.class.php';
require_once dirname(__FILE__) . '/../../publisher/personal_calendar_publisher.class.php';
require_once dirname(__FILE__) . '/../../personal_calendar_rights.class.php';

class PersonalCalendarManagerPublisherComponent extends PersonalCalendarManager implements RepoViewerInterface
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

        if(! PersonalCalendarRights :: is_allowed_in_personal_calendar_subtree(PersonalCalendarRights :: RIGHT_PUBLISH, PersonalCalendarRights :: get_personal_calendar_subtree_root()))
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        

        if (!RepoViewer::is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->run();
        }
        else
        {
            $publisher = new PersonalCalendarPublisher($this);
            $publisher->get_publications_form(RepoViewer::get_selected_objects());
        }
    }

    function get_allowed_content_object_types()
    {
        return array(CalendarEvent :: get_type_name(), Task :: get_type_name(), ExternalCalendar :: get_type_name());
    }
}
?>