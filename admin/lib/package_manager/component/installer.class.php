<?php
/**
 * $Id: installer.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.package_manager.component
 * @author Hans De Bisschop
 */
require_once Path :: get_admin_path() . 'lib/package_manager/component/remote_package_browser/remote_package_browser_table.class.php';

class PackageManagerInstallerComponent extends PackageManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('PlatformAdministration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => AdminManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Admin')));
        $trail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_BROWSE_PACKAGES)), Translation :: get('PackageManager')));
        
        $type = Request :: get('type');
        
        $action = (($type == 'local') ? PackageManager :: ACTION_LOCAL_PACKAGE : PackageManager :: ACTION_REMOTE_PACKAGE);
        
        $trail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_PACKAGE_ACTION => $action)), Translation :: get('Install')));
        $trail->add(new Breadcrumb($this->get_url(array('section' => Request :: get('section'), 'package' => Request :: get('package'), 'type' => $type)), Translation :: get('PackageInstallation')));
        $trail->add_help('administration install');
        
        if (! AdminRights :: is_allowed(AdminRights :: VIEW_RIGHT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $installer = new PackageInstaller();
        $installer->run();
        
        $this->display_header($trail);
        echo $installer->retrieve_result();
        $this->display_footer();
    }
}
?>