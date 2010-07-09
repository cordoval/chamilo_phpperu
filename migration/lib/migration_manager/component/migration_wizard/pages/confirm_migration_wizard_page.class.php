<?php
/**
 * $Id: system_migration_wizard_page.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/migration_wizard_page.class.php';

/**
 * This form can be used to let the user select the settings
 * 
 * @author Sven Vanpoucke
 */
class ConfirmMigrationWizardPage extends MigrationWizardPage
{
 	const SETTING_PLATFORM = 'platform';
    const SETTING_PLATFORM_PATH = 'platform_path';
    const SETTING_MOVE_FILES = 'move_files';
    const SETTING_MIGRATE_DELETED_FILES = 'migrate_deleted_files';
    
    // Default values
    private $defaults;
    
	function display_page_info()
 	{
 		echo Translation :: get('ConfirmationMigrationPageInfo');
 	}  

 	function display_next_page_info()
 	{
 		echo Translation :: get('ConfirmationMigrationPageInfo');
 	}  
 	
    /**
     * Build the form
     */
    function buildForm()
    {
    	$this->build_settings();
    	$this->build_blocks();
		$this->build_clear_settings_info();
		
    	$this->setDefaults($this->defaults);
    	
        $buttons[0] = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Next') . ' >>');
        $this->addGroup($buttons, 'buttons', '', '&nbsp', false);
    }
    
    function build_settings()
    {
    	$this->addElement('category', Translation :: get('Settings'));
    	
    	$settings = array(self :: SETTING_PLATFORM, self :: SETTING_PLATFORM_PATH, self :: SETTING_MOVE_FILES, self :: SETTING_MIGRATE_DELETED_FILES);
    	foreach($settings as $setting)
    	{
    		$value = PlatformSetting :: get($setting, MigrationManager :: APPLICATION_NAME);
    		$this->addElement('text', $setting, Translation :: get($setting));
    		$this->freeze($setting);
    		$this->defaults[$setting] = $value;
    	}
    	
    	$this->addElement('category');
    }
    
    function build_blocks()
    {
    	$this->addElement('category', Translation :: get('Blocks'));
    	
    	$properties = MigrationProperties :: factory(PlatformSetting :: get(self :: SETTING_PLATFORM, MigrationManager :: APPLICATION_NAME));
    	$blocks = $properties->get_migration_blocks();
    	
    	foreach($blocks as $block)
    	{
    		$registration = MigrationDataManager :: retrieve_migration_block_registrations_by_name($block);
    		if($registration)
    		{
    			$value = Translation :: get('True');
    		}
    		else
    		{
    			$value = Translation :: get('False');
    		}
    		
    		$this->addElement('text', $block, Translation :: get('Migrate' . Utilities :: underscores_to_camelcase($block)));
    		$this->freeze($block);
    		$this->defaults[$block] = $value;
    	}
    	
    	$this->addElement('category');
    }
    
    function build_clear_settings_info()
    {
    	$this->addElement('category', Translation :: get('ClearSettings'));
    	
    	$link = $this->get_parent()->get_url(array(MigrationManager :: PARAM_ACTION => MigrationManager :: ACTION_CLEAN_SETTINGS));
    	$this->addElement('html', '<a class="clear_settings_link" href="' . $link . '">' . Translation :: get('ClearSettings') . '</a>');
    	
    	$this->addElement('category');
    }

}
?>