<?php

/**
 * $Id: packager.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.admin_manager.component
 */
/**
 * Admin component
 */
class AdminManagerPackagerComponent extends AdminManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        /*$trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => AdminManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Admin')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Install')));
        $trail->add_help('administration install');*/
        
        if (! AdminRights :: is_allowed(AdminRights :: RIGHT_VIEW))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $package_manager = new PackageManager($this);
        $package_manager->run();
    }
}
?>