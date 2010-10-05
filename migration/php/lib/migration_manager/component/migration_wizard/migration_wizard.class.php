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
require_once dirname(__FILE__) . '/pages/migration_block_migration_wizard_page.class.php';

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
    
    private $blocks;
    private $migrated_blocks = array();
    private $selected_blocks = array();

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
        
        $migration_block_registrations = MigrationDataManager :: get_instance()->retrieve_migration_block_registrations(null, null, null, new ObjectTableOrder(MigrationBlockRegistration :: PROPERTY_ID));
        
        $migrated_blocks = $selected_blocks = array();
        
        while($migration_block_registration = $migration_block_registrations->next_result())
        {
        	$block = $migration_block_registration->get_name();
        	
        	$this->blocks[] = $block;
        	
        	if($migration_block_registration->get_is_migrated())
        	{
        		$this->migrated_blocks[] = $block;
        	}
        	else
        	{
        		$this->selected_blocks[] = $block;
        	}
        	
        	$this->addPage(new MigrationBlockMigrationWizardPage($this, $block . '_migration_page', $block));
        }
        
        $this->addAction('display', new MigrationWizardDisplay($this));
    }
    
    function get_next_block($current_block)
    {
    	if($current_block)
    	{
    		$key = array_search($current_block, $this->get_blocks());
    	}
    	else
    	{
    		return $this->blocks[0];
    	}
    	
    	return $this->blocks[$key + 1];
    }
    
    function get_platform()
    {
    	return $this->platform;
    }
    
    function get_parent()
    {
    	return $this->parent;
    }
    
    function get_blocks()
    {
    	return $this->blocks;
    }
    
    function get_migrated_blocks()
    {
    	return $this->migrated_blocks;
    }
    
    function get_selected_blocks()
    {
    	return $this->selected_blocks;
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