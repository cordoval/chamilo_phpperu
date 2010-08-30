<?php
/**
 * $Id: system_announcement_creator.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package admin.lib.admin_manager.component
 */

class AdminManagerSystemAnnouncementCreatorComponent extends AdminManager implements RepoViewerInterface
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('administration system announcements');

        $repo_viewer = RepoViewer :: construct($this);

        if (! $repo_viewer->is_ready_to_be_published())
        {
            $repo_viewer->run();
        }
        else
        {
            $publisher = new SystemAnnouncerMultipublisher($this);
            $publisher->get_publications_form($repo_viewer->get_selected_objects());
            echo '<div style="clear: both;"></div>';
        }
    }

    function get_allowed_content_object_types()
    {
        return array(SystemAnnouncement :: get_type_name());
    }
}
?>