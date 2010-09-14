<?php
/**
 * $Id: deactivator.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_manager.component
 * @author Hans De Bisschop
 */
class PackageManagerDeactivatorComponent extends PackageManager
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
        
        $ids = Request :: get(PackageManager :: PARAM_REGISTRATION);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $registration = $this->get_parent()->retrieve_registration($id);
                
                $registration->set_status(Registration :: STATUS_INACTIVE);
                if (! $registration->update())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedRegistrationNotDeactivated';
                }
                else
                {
                    $message = 'SelectedRegistrationsNotDeactivated';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedRegistrationDeactivated';
                }
                else
                {
                    $message = 'SelectedRegistrationsDeactivated';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => AdminManager :: ACTION_MANAGE_PACKAGES, PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_BROWSE_PACKAGES));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoRegistrationSelected')));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_BROWSE_PACKAGES)), Translation :: get('PackageManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('admin_package_manager_deactivator');
    }
    
 	function get_additional_parameters()
    {
    	return array(PackageManager :: PARAM_REGISTRATION);
    }
}
?>