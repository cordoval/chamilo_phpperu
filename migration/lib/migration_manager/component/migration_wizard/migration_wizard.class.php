<?php
/**
 * $Id: migration_wizard.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager.component.inc
 */
require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Rule.php';
require_once 'HTML/QuickForm/Action/Display.php';

require_once dirname(__FILE__) . '/pages/migration_wizard_display.class.php';
require_once dirname(__FILE__) . '/pages/migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/pages/confirm_migration_wizard_page.class.php';

/**
 * A wizard which guides the user through several steps to perform the migration
 * 
 * @author Sven Vanpoucke
 */
class MigrationWizard extends HTML_QuickForm_Controller
{
    const SETTING_PLATFORM = 'platform';
    
    /** 
     * The component in which the wizard runs
     */
    private $parent;
    private $platform;

    /**
     * Creates a new MigrationWizard
     * @param MigrationManagerComponent $parent The migrationmanager component 
     * in which this wizard runs.
     */
    function MigrationWizard($parent)
    {
        parent :: HTML_QuickForm_Controller('MigrationWizard', true);
    	$this->parent = $parent;
        $this->platform = PlatformSetting :: get(self :: SETTING_PLATFORM, MigrationManager :: APPLICATION_NAME);
    	
    	$this->addPage(new ConfirmMigrationWizardPage($this, 'confirmation_page'));
        $this->addpages();
        $this->addAction('display', new MigrationWizardDisplay($this));
    }

	/**
     * Creates the pages that belong to a certain old system
     * This pages are defined in wizard.xml in the old system directory
     */
    function addpages()
    {
        
    }
    
    function get_platform()
    {
    	return $this->platform;
    }
    
    function get_parent()
    {
    	return $this->parent;
    }
    
	function display_header()
    {
    	return $this->get_parent()->display_header();
    }
    
    function display_footer()
    {
    	return $this->get_parent()->display_footer();
    }

}
?>