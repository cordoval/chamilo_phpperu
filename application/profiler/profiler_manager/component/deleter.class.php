<?php
/**
 * $Id: deleter.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler.profiler_manager.component
 */
require_once dirname(__FILE__) . '/../profiler_manager.class.php';

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
                if (ProfilerRights::is_allowed_in_profiler_subtree(ProfilerRights::RIGHT_DELETE, $id, ProfilerRights::TYPE_PUBLICATION))
                {
                    $this->display_header();
                    Display :: warning_message(Translation :: get('NotAllowed'));
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
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => ProfilerManager :: ACTION_BROWSE_PROFILES));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoPublicationSelected')));
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