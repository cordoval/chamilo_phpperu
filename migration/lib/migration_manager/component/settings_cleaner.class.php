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
class MigrationManagerSettingsCleanerComponent extends MigrationManager
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
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => MigrationManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Migration') ));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CleanMigrationsettings')));
		$trail->add_help('user general');
		
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
    
    function get_cleaning_methods()
    {
    	$cleaning_methods = array();
    	
    	$cleaning_methods[self :: CLEAN_MIGRATION_BLOCKS] = Translation :: get('CleanMigrationBlockStatus');
    	$cleaning_methods[self :: CLEAN_ALL] = Translation :: get('CleanAll');
    	
    	return $cleaning_methods;
    }
    
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
    
    function clean_migration_blocks()
    {
    	$mdm = MigrationDataManager :: get_instance();
    	$block_registrations = $mdm->retrieve_migration_block_registrations();
    	
    	$succes = true;
    	
    	while($block_registration = $block_registrations->next_result())
    	{
    		$block_registration->set_is_migrated(0);
    		$succes &= $block_registration->update();
    	}
    	
    	return $succes;
    }
    
    function clean_all()
    {
    	// Remove all migration blocks
    	$mdm = MigrationDataManager :: get_instance();
    	$adm = AdminDataManager :: get_instance();
    	$block_registrations = $mdm->retrieve_migration_block_registrations();
    	
    	$succes = true;
    	
    	while($block_registration = $block_registrations->next_result())
    	{
    		$succes &= $block_registration->delete();
    	}
    	
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
    		$setting->update();
    	}
    	
    	return $succes;
    }
}
?>