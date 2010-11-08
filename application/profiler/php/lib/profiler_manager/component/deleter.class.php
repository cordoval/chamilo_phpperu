<?php

namespace application\profiler;

use common\libraries\Request;
use common\libraries\Display;
use common\libraries\Translation;
use common\libraries\Application;
use common\libraries\Breadcrumb;
/**
 * $Id: deleter.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler.profiler_manager.component
 */
class ProfilerManagerDeleterComponent extends ProfilerManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(ProfilerManager :: PARAM_PROFILE_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                if (!ProfilerRights::is_allowed_in_profiler_subtree(ProfilerRights::RIGHT_DELETE, $id, ProfilerRights::TYPE_PUBLICATION))
                {
                    $this->display_header();
                    Display :: warning_message(Translation :: get('NotAllowed', null , Utilities :: COMMON_LIBRARIES));
                    $this->display_footer();
                    exit();

                }
                $publication = $this->retrieve_profile_publication($id);
                
                if (! $publication->delete())
                {
                    $failures ++;
                }
            }
            
     	    if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = Translation :: get('ObjectNotDeleted',array('OBJECT' => Translation :: get('Profile')), Utilities :: COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation :: get('ObjectsNotDeleted',array('OBJECT' => Translation :: get('Profile')), Utilities :: COMMON_LIBRARIES);
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = Translation :: get('ObjectDeleted',array('OBJECT' => Translation :: get('Profile')), Utilities :: COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation :: get('ObjectsDeleted',array('OBJECT' => Translation :: get('Profile')), Utilities :: COMMON_LIBRARIES);
                }
            }
            
            $this->redirect($message, ($failures ? true : false), array(Application :: PARAM_ACTION => ProfilerManager :: ACTION_BROWSE_PROFILES));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectsSelected')));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('profiler_deleter');
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => ProfilerManager :: ACTION_BROWSE_PROFILES)), Translation :: get('ProfilerManagerBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => ProfilerManager :: ACTION_VIEW_PUBLICATION, ProfilerManager :: PARAM_PROFILE_ID => Request :: get(self :: PARAM_PROFILE_ID))), Translation :: get('ProfilerManagerViewerComponent')));
    }

 	function get_additional_parameters()
    {
    	return array(self :: PARAM_PROFILE_ID);
    }
}
?>