<?php
namespace application\personal_calendar;

use common\libraries\WebApplication;
use common\libraries\Display;
use common\extensions\repo_viewer\RepoViewer;
use repository\content_object\calendar_event\CalendarEvent;
use repository\content_object\task\Task;
use repository\content_object\external_calendar\ExternalCalendar;
use common\libraries\Breadcrumb;
use common\extensions\repo_viewer\RepoViewerInterface;
use common\libraries\Application;
use common\libraries\Translation;
/**
 * $Id: publisher.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.personal_calendar.personal_calendar_manager.component
 */
require_once WebApplication :: get_application_class_lib_path('personal_calendar') . 'publisher/personal_calendar_publisher.class.php';
require_once WebApplication :: get_application_class_lib_path('personal_calendar') . 'personal_calendar_rights.class.php';

class PersonalCalendarManagerPublisherComponent extends PersonalCalendarManager implements RepoViewerInterface
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        if(! PersonalCalendarRights :: is_allowed(PersonalCalendarRights :: RIGHT_PUBLISH, PersonalCalendarRights :: get_root()))
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed", null , Utilities :: COMMON_LIBRARIES));
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
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR)), Translation :: get('PersonalCalendarManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('personal_calendar_publisher');
    }
    
}
?>