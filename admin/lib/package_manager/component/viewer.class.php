<?php
require_once dirname(__FILE__) . '/../../registration_viewer/registration_display.class.php';
class PackageManagerViewerComponent extends PackageManager
{
	private $action_bar;
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
    	$id = Request :: get(PackageManager :: PARAM_REGISTRATION);
       	$registration = $this->get_parent()->retrieve_registration($id);
       	
    	$trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
        $trail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_BROWSE_PACKAGES)), Translation :: get('PackageManager')));
    	$trail->add(new Breadcrumb($this->get_url(), Translation :: get(Utilities::camelcase_to_underscores($registration->get_name()))));
       	
       	$registration_display = new RegistrationDisplay($registration);
       	$this->display_header();
       	$this->action_bar = $this->get_action_bar($registration);
       	echo($this->action_bar->as_html());
       	
       	echo($registration_display->as_html());
       	$this->display_footer();
    }
    
    function get_action_bar($registration)
    {
    	$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
    	        
        if (! $registration->is_up_to_date())
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('UpdatePackage'), Theme :: get_common_image_path() . 'action_update.png', $this->get_registration_update_url($registration), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        else
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('PackageIsAlreadyUpToDate'), Theme :: get_common_image_path() . 'action_update_na.png',null, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('UpdatePackageFromArchive'), Theme :: get_image_path() . 'action_update_archive.png', $this->get_registration_update_archive_url($registration), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

//        if ($registration->get_type() == Registration :: TYPE_LANGUAGE && Utilities :: camelcase_to_underscores($registration->get_name()) == PlatformSetting :: get('platform_language'))
//        {
//            return;
//        }

        if ($registration->is_active())
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Deactivate'), Theme :: get_common_image_path() . 'action_deactivate.png', $this->get_registration_deactivation_url($registration), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        else
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Activate'), Theme :: get_common_image_path() . 'action_activate.png', $this->get_registration_activation_url($registration), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Deinstall'), Theme :: get_common_image_path() . 'action_deinstall.png', $this->get_registration_removal_url($registration), ToolbarItem :: DISPLAY_ICON_AND_LABEL, true));

        return $action_bar;
    }
}
?>