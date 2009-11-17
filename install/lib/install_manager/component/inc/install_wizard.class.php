<?php
/**
 * $Id: install_wizard.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 * @package install.lib.install_manager.component.inc
 */
require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Rule.php';
require_once 'HTML/QuickForm/Action/Display.php';

require_once dirname(__FILE__) . '/wizard/language_install_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/requirements_install_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/license_install_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/database_install_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/application_install_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/settings_install_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/install_wizard_process.class.php';
require_once dirname(__FILE__) . '/wizard/install_wizard_display.class.php';
/**
 * A wizard which guides the user to several steps to complete a maintenance
 * action on a course.
 */
class InstallWizard extends HTML_QuickForm_Controller
{
    /**
     * The repository tool in which this wizard runs.
     */
    private $parent;

    /**
     * Creates a new MaintenanceWizard
     * @param Tool $parent The repository tool in which this wizard
     * runs.
     */
    function InstallWizard($parent)
    {
        global $language_interface;
        //$language_interface = 'english';
        $this->parent = $parent;
        parent :: HTML_QuickForm_Controller('InstallWizard', true);
        $this->addPage(new LanguageInstallWizardPage('page_language', $this->parent));
        $this->addPage(new RequirementsInstallWizardPage('page_requirements', $this->parent));
        $this->addPage(new LicenseInstallWizardPage('page_license', $this->parent));
        $this->addPage(new DatabaseInstallWizardPage('page_database', $this->parent));
        $this->addPage(new ApplicationInstallWizardPage('page_application', $this->parent));
        $this->addPage(new SettingsInstallWizardPage('page_settings', $this->parent));
        
        $this->addAction('process', new InstallWizardProcess($this->parent));
        $this->addAction('display', new InstallWizardDisplay($this->parent));
    }
}
?>