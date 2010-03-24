<?php
/**
 * $Id: installer.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 * @package install.lib.installmanager.component
 * 
 * @author Hans De Bisschop
 */
require_once dirname(__FILE__) . '/../install_manager.class.php';
require_once dirname(__FILE__) . '/../install_manager_component.class.php';
require_once dirname(__FILE__) . '/inc/install_wizard.class.php';
/**
 * Installer install manager component which allows the user to install the platform
 */
class InstallManagerInstallerComponent extends InstallManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
    	$already_installed = false;
    	// include the main Chamilo platform configuration file
		$main_configuration_file_path = $includePath . '/configuration/configuration.php';
		
		if (file_exists($main_configuration_file_path))
		{
    		$already_installed = true;
		}
		
		if($already_installed && (PlatformSetting :: get('installation_blocked') == TRUE))
		{
			//display warning: installation is blocked by administrator	
			$this->display_header();
    		Display :: error_message(Translation:: get('InstallationBlockedByAdministrator'));
    		$this->display_footer();
			
		}
		else
    	{
        	$wizard = new InstallWizard($this);
        	$wizard->run();	
		}
		
    }
}
?>
