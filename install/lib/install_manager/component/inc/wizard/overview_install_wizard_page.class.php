<?php
/**
 * $Id: settings_install_wizard_page.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 * @package install.lib.installmanager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/install_wizard_page.class.php';
/**
 * Page in the install wizard in which some config settings are asked to the
 * user.
 */
class OverviewInstallWizardPage extends InstallWizardPage
{

    function get_title()
    {
        return Translation :: get('InstallationOverview');
    }

    function get_info()
    {
        return Translation :: get('InstallationOverviewInfo');
    }

    function buildForm()
    {
        $this->set_lang($this->controller->exportValue('page_language', 'install_language'));
        $this->_formBuilt = true;
        
        $this->addElement('category', Translation :: get('DatabaseProperties'));
        $this->addElement('static', 'database_driver', Translation :: get('DBDriver'));
        $this->addElement('static', 'database_host', Translation :: get('DBHost'));
        $this->addElement('static', 'database_name', Translation :: get('DatabaseName'));
        $this->addElement('static', 'database_username', Translation :: get('DBLogin'));
        $this->addElement('static', 'database_password', Translation :: get('DBPassword'));
        $this->addElement('Static', 'database_exists', Translation :: get('DatabaseExists'));
        $this->addElement('category');
        
        $this->addElement('category', Translation :: get('Applications'));
        $this->addElement('static', 'selected_applications', Translation :: get('SelectedApplications'));
        $this->addElement('static', 'non_selected_applications', Translation :: get('NonSelectedApplications'));
        $this->addElement('category');
        

        $this->addElement('category', Translation :: get('GeneralProperties'));
        $this->addElement('static', 'platform_language', Translation :: get('MainLang'));
        $this->addElement('static', 'platform_url', Translation :: get('ChamiloURL'));
        $this->addElement('category');

        $this->addElement('category', Translation :: get('Administrator'));
        $this->addElement('static', 'admin_email', Translation :: get('AdminEmail'));
        $this->addElement('static', 'admin_surname', Translation :: get('AdminLastName'));
        $this->addElement('static', 'admin_firstname', Translation :: get('AdminFirstName'));
        $this->addElement('static', 'admin_phone', Translation :: get('AdminPhone'));
        $this->addElement('static', 'admin_username', Translation :: get('AdminLogin'));
        $this->addElement('static', 'admin_password', Translation :: get('AdminPass'));
        $this->addElement('category');

        $this->addElement('category', Translation :: get('Platform'));
        $this->addElement('static', 'platform_name', Translation :: get('CampusName'));
        $this->addElement('static', 'organization_name', Translation :: get('InstituteShortName'));
        $this->addElement('static', 'organization_url', Translation :: get('InstituteURL'));
        $this->addElement('category');

        $this->addElement('category', Translation :: get('Security'));
        $this->addElement('static', 'self_reg', Translation :: get('AllowSelfReg'));
        $this->addElement('static', 'hashing_algorithm', Translation :: get('HashingAlgorithm'));
        $this->addElement('category');

        $buttons = array();
        $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('back'), Translation :: get('Previous'), array('class' => 'normal previous'));
        $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('submit'), Translation :: get('Finish'), array('class' => 'positive'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->setDefaultAction($this->getButtonName('submit'));
        $this->set_form_defaults();
    }

    function set_form_defaults()
    {
        $defaults = array();
        
        // Database settings
        $defaults['database_driver']	= $this->controller->exportValue('page_database', 'database_driver');
        $defaults['database_host']		= $this->controller->exportValue('page_database', 'database_host');
        $defaults['database_name']		= $this->controller->exportValue('page_database', 'database_name');
        $defaults['database_username']	= $this->controller->exportValue('page_database', 'database_username');
        $defaults['database_password']	= $this->controller->exportValue('page_database', 'database_password');
        $defaults['database_exists'] 	= $this->controller->exportValue('page_database', 'database_exists') ? Translation :: get('Yes') : Translation :: get('No');
        
        // Application selections
    	$applications = Filesystem :: get_directory_content(Path :: get_application_path() . 'lib/', Filesystem :: LIST_DIRECTORIES, false);
    	$selected_applications = array();
    	$non_selected_applications = array();
    	
    	foreach ($applications as $application)
    	{
    		$check_name = 'install_' . $application;
    		$value = $this->controller->exportValue('page_application', $check_name);
    		
    		if (isset($value) && $value == '1')
    		{
    			$selected_applications[] = Translation :: get(Utilities :: underscores_to_camelcase($application)); 
    		}
    		else
    		{
    			$non_selected_applications[] = Translation :: get(Utilities :: underscores_to_camelcase($application));
    		}
    	}
    	
    	$defaults['selected_applications'] = implode("<br />\n", $selected_applications);
    	$defaults['non_selected_applications'] = implode("<br />\n", $non_selected_applications);
        
        // Platform settings
        $defaults['platform_language']	= $this->controller->exportValue('page_language', 'install_language');
        $defaults['platform_url']		= $this->controller->exportValue('page_settings', 'platform_url');
        $defaults['admin_email']		= $this->controller->exportValue('page_settings', 'admin_email');
        $defaults['admin_surname']		= $this->controller->exportValue('page_settings', 'admin_surname');
        $defaults['admin_firstname']	= $this->controller->exportValue('page_settings', 'admin_firstname');
        $defaults['admin_username']		= $this->controller->exportValue('page_settings', 'admin_username');
        $defaults['admin_password']		= $this->controller->exportValue('page_settings', 'admin_password');
        $defaults['platform_name']		= $this->controller->exportValue('page_settings', 'platform_name');
        $defaults['organization_name']	= $this->controller->exportValue('page_settings', 'organization_name');
        $defaults['organization_url']	= $this->controller->exportValue('page_settings', 'organization_url');        
        $defaults['self_reg']			= Translation :: get(($this->controller->exportValue('page_settings', 'self_reg') == 1 ? 'Yes' : 'No'));
        $defaults['hashing_algorithm']	= $this->controller->exportValue('page_settings', 'hashing_algorithm');
        $this->setDefaults($defaults);
    }
}
?>