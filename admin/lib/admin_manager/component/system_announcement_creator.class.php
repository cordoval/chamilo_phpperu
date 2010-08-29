<?php
/**
 * $Id: system_announcement_creator.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package admin.lib.admin_manager.component
 */

class AdminManagerSystemAnnouncementCreatorComponent extends AdminManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('administration system announcements');

        $repo_viewer = new RepoViewer($this, SystemAnnouncement :: get_type_name());

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
}
?>