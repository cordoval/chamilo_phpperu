<?php

/**
 * $Id: configurer.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.admin_manager.component
 */
/**
 * Admin component
 */
class AdminManagerConfigurerComponent extends AdminManager implements AdministrationComponent
{
    private $application;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $application = $this->application = Request :: get(AdminManager :: PARAM_WEB_APPLICATION);
        if (! isset($application))
        {
            $application = $this->application = 'admin';
        }

        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(AdminManager :: PARAM_WEB_APPLICATION => $application)), Translation :: get(Utilities :: underscores_to_camelcase($application))));

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
    	$breadcrumbtrail->add_help('admin_configurer');
    }
}
?>