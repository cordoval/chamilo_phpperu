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
    const SETTING_PLATFORM_PATH = 'platform_path';
    const SETTING_MOVE_FILES = 'move_files';
    const SETTING_MIGRATE_DELETED_FILES = 'migrate_deleted_files';
    
    const PARAM_NOT_SELECTED_BLOCKS = 'not_selected_blocks';
    const PARAM_SELECTED_BLOCKS = 'selected_blocks';
    const PARAM_SELECTED_MIGRATED_BLOCKS = 'selected_migrated_blocks';
    
    // Default values
    private $defaults;
    
    // The settings that need to be validated by the migration properties
    private $validate_settings = array();
    
    // The blocks that need to be validated by the migration properties
    private $validate_blocks = array();
    
    /**
     * The migration properties - Used to retrieve the blocks and validate the settings
     * @var MigrationProperties
     */
    private $migration_properties;
    
    /**
     * Check wether the settings are valid or not 
     * Used to determine wheter we can use the next button or not
     * @var boolean
     */
    private $is_valid;
    
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
    	$this->migration_properties = MigrationProperties :: factory($this->get_platform());
    	
    	$this->build_settings();
    	$this->build_blocks();
    	$this->build_validate_settings();
		$this->build_clear_settings_info();
		
    	$this->setDefaults($this->defaults);
    	
        $buttons[0] = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Next') . ' >>');
        $this->addGroup($buttons, 'buttons', '', '&nbsp', false);
    }
    
    /**
     * Build the general settings form
     */
    function build_settings()
    {
    	$this->addElement('category', Translation :: get('Settings'));
    	
    	$this->addElement('static', MigrationWizard :: SETTING_PLATFORM, Translation :: get(Utilities :: underscores_to_camelcase(MigrationWizard :: SETTING_PLATFORM)));
    	$this->defaults[MigrationWizard :: SETTING_PLATFORM] = $this->get_platform();
    	
    	$settings = array(self :: SETTING_PLATFORM_PATH, self :: SETTING_MOVE_FILES, self :: SETTING_MIGRATE_DELETED_FILES);
    	foreach($settings as $setting)
    	{
    		$value = PlatformSetting :: get($setting, MigrationManager :: APPLICATION_NAME);
    		$this->addElement('static', $setting, Translation :: get(Utilities :: underscores_to_camelcase($setting)));
    		$this->defaults[$setting] = $value;
    		$this->validate_settings[$setting] = $value;
    	}
    	
    	$this->addElement('category');
    }
    
    /**
     * Build the blocks form (selected migrated blocks, selected blocks, not selected blocks)
     */
    function build_blocks()
    {
    	$this->addElement('category', Translation :: get('Blocks'));
    	
    	$blocks = $this->migration_properties->get_migration_blocks();
    	
    	$selected_migrated_blocks = $selected_blocks = $not_selected_blocks = array();
    	
    	foreach($blocks as $block)
    	{
    		$registration = MigrationDataManager :: retrieve_migration_block_registrations_by_name($block);
    		$block_name = Translation :: get('Migrate' . Utilities :: underscores_to_camelcase($block)); 
    		
    		if($registration)
    		{
    			if($registration->get_is_migrated())
    			{
    				$selected_migrated_blocks[] = $block_name;
    			}
    			else
    			{
	    			$selected_blocks[] =  $block_name;
	    			$this->validate_blocks[] = $block;
    			}
    		}
    		else
    		{
    			$not_selected_blocks[] = $block_name;
    		}
    	}
    	
    	if($selected_migrated_blocks)
    	{
    		$this->defaults[self :: PARAM_SELECTED_MIGRATED_BLOCKS] = implode("<br />\n", $selected_migrated_blocks);
    	}
    	else
    	{
    		$this->defaults[self :: PARAM_SELECTED_MIGRATED_BLOCKS] = '-';
    	}
    	
    	if($selected_blocks)
    	{
    		$this->defaults[self :: PARAM_SELECTED_BLOCKS] = implode("<br />\n", $selected_blocks);
    	}
    	else
    	{
    		$this->defaults[self :: PARAM_SELECTED_BLOCKS] = '-';
    	}
    	
    	if($not_selected_blocks)
    	{
    		$this->defaults[self :: PARAM_NOT_SELECTED_BLOCKS] = implode("<br />\n", $not_selected_blocks);
    	}
    	else
    	{
    		$this->defaults[self :: PARAM_NOT_SELECTED_BLOCKS] = '-';
    	}
    	
    	$this->addElement('static', self :: PARAM_SELECTED_MIGRATED_BLOCKS, Translation :: get('SelectedMigratedBlocks'));
    	$this->addElement('static', self :: PARAM_SELECTED_BLOCKS, Translation :: get('SelectedNotMigratedBlocks'));
    	$this->addElement('static', self :: PARAM_NOT_SELECTED_BLOCKS, Translation :: get('NotSelectedBlocks'));
    	
    	$this->addElement('category');
    }
    
    /**
     * Build the validate settings block
     * Uses the migration properties to validate the settings
     */
    function build_validate_settings()
    {
    	$this->addElement('category', Translation :: get('SettingsValidation'));
    	
    	$this->is_valid = $this->migration_properties->validate_settings($this->validate_settings, $this->validate_blocks);
    	if($this->is_valid)
    	{
    		$html = '<div class="normal-message">' . Translation :: get('AllSettingsValid') . '</div>';
    	}
    	else
    	{
    		$html = '<div class="error-message">' . Translation :: get('SettingsNotValid') . '</div>';
    	}
    	
    	$this->addElement('html', $html);
    	
    	$this->addElement('category');
    }
    
    /**
     * Build the link to the clear settings component
     */
    function build_clear_settings_info()
    {
    	$this->addElement('category', Translation :: get('ClearSettings'));
    	
    	$link = $this->get_parent()->get_parent()->get_url(array(MigrationManager :: PARAM_ACTION => MigrationManager :: ACTION_CLEAN_SETTINGS));
    	$this->addElement('html', '<a class="clear_settings_link" href="' . $link . '">' . Translation :: get('ClearSettings') . '</a>');
    	
    	$this->addElement('category');
    }

}
?>