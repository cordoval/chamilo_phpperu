<?php
/**
 * $Id: remover.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.package_manager.component
 * @author Hans De Bisschop
 */
require_once Path :: get_admin_path() . 'lib/package_manager/component/remote_package_browser/remote_package_browser_table.class.php';

class PackageManagerRemoverComponent extends PackageManager
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
        
        $type = Request :: get(PackageManager :: PARAM_SECTION);
        
        if ($type)
        {
            $remover = PackageRemover :: factory($type, $this);
            $remover->run();
            
            $this->display_header();
            echo $remover->retrieve_result();
            $this->display_footer();
        }
        else
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NoPackageTypeDefined'));
            $this->display_footer();
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_BROWSE_PACKAGES)), Translation :: get('PackageManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('admin_package_manager_remover');
    }
    
 	function get_additional_parameters()
    {
    	return array(PackageManager :: PARAM_SECTION, PackageManager :: PARAM_PACKAGE);
    }
}
?>