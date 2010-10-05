<?php
/**
 * $Id: migration.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager.component
 */

/**
 * Migration MigrationManagerComponent which allows the administrator to clean settings
 *
 * @author Sven Vanpoucke
 */
class MigrationManagerSettingsCleanerComponent extends MigrationManager implements AdministrationComponent
{
	const CLEANING_METHOD = 'cleaning_method';
	const CLEAN_MIGRATION_BLOCKS = 1;
	const CLEAN_ALL = 2;
	
	const SETTING_PLATFORM = 'platform';
    const SETTING_PLATFORM_PATH = 'platform_path';
    const SETTING_MOVE_FILES = 'move_files';
    const SETTING_MIGRATE_DELETED_FILES = 'migrate_deleted_files';
    const SETTING_IN_MIGRATION = 'in_migration'; 
	
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
		$form = $this->create_settings_cleaner_form();
		
		if($form->validate())
		{
			$succes = $this->clean_settings($form);
			$message = $succes ? Translation :: get('SettingsCleaned') : Translation :: get('SettingsNotCleaned');
			$this->redirect($message, !$succes);
			
		}
		else
		{
			$this->display_header();
			$form->display();
			$this->display_footer();
		}
    }
    
    function create_settings_cleaner_form()
    {
    	$form = new FormValidator('migration_settings_cleaner', 'post', $this->get_url());
    	
    	$form->addElement('select', self :: CLEANING_METHOD, Translation :: get('CleaningMethod'), $this->get_cleaning_methods());
    	$form->addElement('style_submit_button', FormValidator :: PARAM_SUBMIT, Translation :: get('Clean'), array('class' => 'positive update'));
    	
    	return $form;
    }
    
    /**
     * Retrieves the cleaning methods
     */
    function get_cleaning_methods()
    {
    	$cleaning_methods = array();
    	
    	$cleaning_methods[self :: CLEAN_MIGRATION_BLOCKS] = Translation :: get('CleanMigrationBlockStatus');
    	$cleaning_methods[self :: CLEAN_ALL] = Translation :: get('CleanAll');
    	
    	return $cleaning_methods;
    }
    
    /**
     * Cleans the settings with the selected cleaning method
     */
    function clean_settings($form)
    {
    	$cleaning_method = $form->exportValue(self :: CLEANING_METHOD);
    	
    	switch($cleaning_method)
    	{
    		case self :: CLEAN_MIGRATION_BLOCKS:
    				return $this->clean_migration_blocks();
    		case self :: CLEAN_ALL:
    				return $this->clean_all();
    	}
    }
    
    /**
     * Cleans the migration blocks
     */
    function clean_migration_blocks()
    {
    	$succes = MigrationDataManager :: get_instance()->reset_migration_block_registration_status();
    	$succes &= $this->truncate_databases();
    	
    	return $succes;
    }
    
    /**
     * Cleans everything
     */
    function clean_all()
    {
    	// Remove all migration blocks
    	$mdm = MigrationDataManager :: get_instance();
    	$adm = AdminDataManager :: get_instance();
    	
    	$succes = $this->truncate_databases();
    	$succes &= $mdm->truncate_migration_block_registrations();
    	
    	// Remove all settings
    	$settings = array(self :: SETTING_PLATFORM, self :: SETTING_PLATFORM_PATH, self :: SETTING_MOVE_FILES, self :: SETTING_MIGRATE_DELETED_FILES, self :: SETTING_IN_MIGRATION);
    	foreach($settings as $setting)
    	{
    		$setting = $adm->retrieve_setting_from_variable_name($setting, MigrationManager :: APPLICATION_NAME);
    		
    		$value = $setting->get_value();
    		if(is_numeric($value))
    		{
    			$new_value = 0;
    		}
    		else
    		{
    			$new_value = '-';
    		}
    		
    		$setting->set_value($new_value);
    		$succes &= $setting->update();
    	}
    	
    	return $succes;
    }
    
    /**
     * Truncates the databases failed elements, file recoveries, id references
     */
    function truncate_databases()
    {
    	$mdm = MigrationDataManager :: get_instance();
    	
    	$succes = $mdm->truncate_failed_elements(); 
    	$succes &= $mdm->truncate_file_recoveries();
    	$succes &= $mdm->truncate_id_references();
    	return $succes;
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('migration_settings_cleaner');
    }
}
?>