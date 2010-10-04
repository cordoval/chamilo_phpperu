<?php

/**
 * $Id: configurer.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.admin_manager.component
 */
/**
 * Admin component
 */
class AdminManagerConfigurerComponent extends AdminManager
{
    private $application;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $application = $this->get_application();
        
        if (! AdminRights :: is_allowed(AdminRights :: RIGHT_VIEW, AdminRights :: LOCATION_SETTINGS, AdminRights :: TYPE_ADMIN_COMPONENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }

        $form = new ConfigurationForm($application, 'config', 'post', $this->get_url(array(AdminManager :: PARAM_WEB_APPLICATION => $application)));

        if ($form->validate())
        {
            $success = $form->update_configuration();
            $this->redirect(Translation :: get($success ? 'ConfigurationUpdated' : 'ConfigurationNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => AdminManager :: ACTION_CONFIGURE_PLATFORM, AdminManager :: PARAM_WEB_APPLICATION => $application));
        }
        else
        {
            $this->display_header();
            $application_url = $this->get_url(array(Application :: PARAM_ACTION => AdminManager :: ACTION_CONFIGURE_PLATFORM, AdminManager :: PARAM_WEB_APPLICATION => Application :: PLACEHOLDER_APPLICATION));
            echo BasicApplication :: get_selecter($application_url, $this->application);
            $form->display();
            echo '<script type="text/javascript">';
            echo '$(document).ready(function() {';
            echo '$(\':checkbox\').iphoneStyle({ checkedLabel: \'' . Translation :: get('On') . '\', uncheckedLabel: \'' . Translation :: get('Off') . '\'});';
            echo '});';
            echo '</script>';
            $this->display_footer();
        }
    }
    
    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $application = $this->get_application();
        
    	$breadcrumbtrail->truncate(true);
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => null)), Translation :: get('Administration')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => $application)), Translation :: get(Utilities :: underscores_to_camelcase($application))));
        //$breadcrumbtrail->add(new Breadcrumb($this->get_url(), Translation :: get('AdminManagerConfigurerComponent')));
        
    	$breadcrumbtrail->add_help('admin_configurer');
    }
    
	function get_additional_parameters()
    {
    	return array(AdminManager :: PARAM_WEB_APPLICATION);
    }
    
    function get_application()
    {
    	$application = $this->application = Request :: get(AdminManager :: PARAM_WEB_APPLICATION);
        if (! isset($application))
        {
            $application = $this->application = 'admin';
        }
    	
        return $application;
    }
}
?>