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
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => AdminManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Admin')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AdminManager :: ACTION_BROWSE_SYSTEM_ANNOUNCEMENTS)), Translation :: get('SystemAnnouncements')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('PublishSystemAnnouncement')));
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