<?php
/**
 * $Id: attachment_viewer.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar.personal_calendar_manager.component
 */

class PersonalCalendarManagerAttachmentViewerComponent extends PersonalCalendarManager
{

    function run()
    {
        $object_id = Request :: get('object');
        
        if ($object_id)
        {
            $this->display_header();
            
            echo '<a href="javascript:history.go(-1)">' . Translation :: get('Back') . '</a><br /><br />';
            
            $object = RepositoryDataManager :: get_instance()->retrieve_content_object($object_id);
            $display = ContentObjectDisplay :: factory($object);
            
            echo $display->get_full_html();
            
            $this->display_footer();
        
        }
        else
        {
            $this->display_header();
            $this->display_error_message('NoObjectSelected');
            $this->display_footer();
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR)), Translation :: get('PersonalCalendarManagerBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_VIEW_PUBLICATION, self :: PARAM_PERSONAL_CALENDAR_ID => Request :: get(self :: PARAM_PERSONAL_CALENDAR_ID))), Translation :: get('PersonalCalendarManagerViewerComponent')));
    	$breadcrumbtrail->add_help('personal_calendar_attachment_viewer');
    }
    
    function get_additional_parameters()
    {
    	return array('object');
    }
}
?>