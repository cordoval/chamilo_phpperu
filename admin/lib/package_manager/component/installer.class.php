<?php
/**
 * $Id: installer.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.package_manager.component
 * @author Hans De Bisschop
 */
require_once Path :: get_admin_path() . 'lib/package_manager/component/remote_package_browser/remote_package_browser_table.class.php';

class PackageManagerInstallerComponent extends PackageManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        if (! AdminRights :: is_allowed(AdminRights :: RIGHT_VIEW))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $installer = new PackageInstaller();
        $installer->run();
        
        $this->display_header();
        echo $installer->retrieve_result();
        $this->display_footer();
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_BROWSE_PACKAGES)), Translation :: get('PackageManagerBrowserComponent')));
    	
        $type = Request :: get(PackageManager :: PARAM_INSTALL_TYPE);
        
        if($type == 'local')
        {
        	$action = PackageManager :: ACTION_LOCAL_PACKAGE;
        	$message = 'PackageManagerLocalComponent';
        }
        else
        {
        	$action = PackageManager :: ACTION_REMOTE_PACKAGE;
        	$message = 'PackageManagerRemoteComponent';
        }
        
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_PACKAGE_ACTION => $action, PackageManager :: PARAM_SECTION => Request :: get(PackageManager :: PARAM_SECTION), 
        		PackageManager :: PARAM_PACKAGE => Request :: get(PackageManager :: PARAM_PACKAGE), PackageManager :: PARAM_INSTALL_TYPE => $type)), Translation :: get($message)));
    	
    	$breadcrumbtrail->add_help('admin_package_manager_installer');
    }
    
 	function get_additional_parameters()
    {
    	return array(PackageManager :: PARAM_INSTALL_TYPE, PackageManager :: PARAM_PACKAGE, PackageManager :: PARAM_SECTION);
    }
}
?>