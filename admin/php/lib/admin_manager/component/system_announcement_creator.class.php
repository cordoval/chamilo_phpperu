<?php
/**
 * $Id: system_announcement_creator.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package admin.lib.admin_manager.component
 */

class AdminManagerSystemAnnouncementCreatorComponent extends AdminManager implements RepoViewerInterface, AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        if (! RepoViewer :: is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->run();
        }
        else
        {
            $publisher = new SystemAnnouncerMultipublisher($this);
            $publisher->get_publications_form(RepoViewer :: get_selected_objects());
            echo '<div style="clear: both;"></div>';
        }
    }

    function get_allowed_content_object_types()
    {
        return array(SystemAnnouncement :: get_type_name());
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_BROWSE_SYSTEM_ANNOUNCEMENTS)), Translation :: get('AdminManagerSystemAnnouncementBrowserComponent')));
    	$breadcrumbtrail->add_help('admin_system_announcement_creator');
    }
}
?>