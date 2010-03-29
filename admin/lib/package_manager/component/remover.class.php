<?php
/**
 * $Id: remover.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.package_manager.component
 * @author Hans De Bisschop
 */
require_once Path :: get_admin_path() . 'lib/package_manager/component/remote_package_browser/remote_package_browser_table.class.php';

class PackageManagerRemoverComponent extends PackageManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('PlatformAdmin')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => AdminManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Admin')));
        $trail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_BROWSE_PACKAGES)), Translation :: get('PackageManager')));
        $parameters[PackageManager :: PARAM_SECTION] = Request :: get(PackageManager :: PARAM_SECTION);
        $parameters[PackageManager :: PARAM_PACKAGE] = Request :: get(PackageManager :: PARAM_PACKAGE);
        $trail->add(new Breadcrumb($this->get_url($parameters), Translation :: get('PackageRemoval')));
        $trail->add_help('administration remove');
        
        if (! AdminRights :: is_allowed(AdminRights :: VIEW_RIGHT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $type = Request :: get(PackageManager :: PARAM_SECTION);
        
        if ($type)
        {
            $remover = PackageRemover :: factory($type, $this);
            $remover->run();
            
            $this->display_header($trail);
            echo $remover->retrieve_result();
            $this->display_footer();
        }
        else
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NoPackageTypeDefined'));
            $this->display_footer();
        }
    }
}
?>