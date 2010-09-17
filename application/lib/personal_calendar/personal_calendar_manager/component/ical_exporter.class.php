<?php
/**
 * $Id: ical_exporter.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar.personal_calendar_manager.component
 */
require_once dirname(__FILE__) . '/../personal_calendar_manager.class.php';
require_once dirname(__FILE__) . '/../../personal_calendar_rights.class.php';

class PersonalCalendarManagerIcalExporterComponent extends PersonalCalendarManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(PersonalCalendarManager :: PARAM_PERSONAL_CALENDAR_ID);
        
        if ($id)
        {
            $calendar_event_publication = $this->retrieve_personal_calendar_publication($id);
            $content_object = $calendar_event_publication->get_publication_object();

            if(! PersonalCalendarRights :: is_allowed_in_personal_calendar_subtree(PersonalCalendarRights :: RIGHT_SHARE, PersonalCalendarRights :: get_personal_calendar_subtree_root()))
            {
                $this->display_header();
                Display :: error_message(Translation :: get("NotAllowed"));
                $this->display_footer();
                exit();
            }
            
            $exporter = ContentObjectExport :: factory('ical', $content_object);
            $path = $exporter->export_content_object();
            Filesystem :: file_send_for_download($path, true, basename($path));
            Filesystem :: remove($path);
        }
        else
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NoObjectSelected'));
            $this->dipslay_footer();
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR)), Translation :: get('PersonalCalendarManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('personal_calendar_ical_exporter');
    }
    
    function get_additional_parameters()
    {
    	return array(self :: PARAM_PERSONAL_CALENDAR_ID);
    }

}
?>