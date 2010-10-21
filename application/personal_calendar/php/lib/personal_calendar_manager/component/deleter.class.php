<?php

namespace personal_calendar;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Application;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;

/**
 * $Id: deleter.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar.personal_calendar_manager.component
 */

class PersonalCalendarManagerDeleterComponent extends PersonalCalendarManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(PersonalCalendarManager :: PARAM_PERSONAL_CALENDAR_ID);
        $failures = 0;
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $publication = $this->retrieve_personal_calendar_publication($id);
                
                if (! $this->get_user()->is_platform_admin() && $publication->get_publisher() != $this->get_user_id())
                {
                    $this->display_header();
                    $this->display_error_message(Translation :: get('NotAllowed'));
                    $this->display_footer();
                    exit();
                }
                
                if (! $publication->delete())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedPublicationNotDeleted';
                }
                else
                {
                    $message = 'SelectedPublicationsNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedPublicationDeleted';
                }
                else
                {
                    $message = 'SelectedPublicationsDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoPublicationSelected')));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR)), Translation :: get('PersonalCalendarManagerBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_VIEW_PUBLICATION, self :: PARAM_PERSONAL_CALENDAR_ID => Request :: get(self :: PARAM_PERSONAL_CALENDAR_ID))), Translation :: get('PersonalCalendarManagerViewerComponent')));
    	$breadcrumbtrail->add_help('personal_calendar_deleter');
    }
    
    function get_additional_parameters()
    {
    	return array(self :: PARAM_PERSONAL_CALENDAR_ID);
    }
}
?>