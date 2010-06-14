<?php
/**
 * $Id: updater.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.package_manager.component
 * @author Hans De Bisschop
 */

class PackageManagerUpdaterComponent extends PackageManager
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
        //$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => AdminManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Admin')));
        //$trail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_BROWSE_PACKAGES)), Translation :: get('PackageManager')));

        //$type = Request :: get('type');
        //$action = (($type == 'local') ? PackageManager :: ACTION_LOCAL_PACKAGE : PackageManager :: ACTION_REMOTE_PACKAGE);
        //$trail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_PACKAGE_ACTION => $action)), Translation :: get('Install')));
        //$trail->add(new Breadcrumb($this->get_url(array('section' => Request :: get('section'), 'package' => Request :: get('package'), 'type' => $type)), Translation :: get('PackageUpdate')));
        //$trail->add_help('administration update');

        if (! AdminRights :: is_allowed(AdminRights :: VIEW_RIGHT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }

        $updater = new PackageUpdater();
        $updater->run();

        $this->display_header();
        echo $updater->retrieve_result();
        $this->display_footer();
    }
}
?>