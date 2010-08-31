<?php
/**
 * @package migration
 * @author Sven Vanpoucke
 */

require_once dirname(__FILE__) . '/../../common/global.inc.php';
ini_set("memory_limit", "-1");
set_time_limit(0);

Utilities :: set_application('migration');

//Temporary because i can't test command line with pear on mac os x
echo '<pre>';

$setting = PlatformSetting :: get('in_migration', MigrationManager :: APPLICATION_NAME);
if($setting != 1)
{
	$validate_settings = array();
	$selected_blocks = array();
	$migrated_blocks = array();
	
	$settings_file = dirname(__FILE__) . '/command_line_migration_settings.php';
	if(!file_exists($settings_file))
	{
		die(Translation :: get('NoCommandLineMigrationSettingsFileFound'));
	}
	
	$settings = array();
	require_once $settings_file;
	
	$adm = AdminDataManager :: get_instance();
	
	$setting_names = array('platform', 'platform_path', 'move_files', 'migrate_deleted_files');
	foreach($setting_names as $setting_name)
	{
		$value = $settings[$setting_name];
		if(is_null($value))
	    {
	    	$value = 0;
	    }
	    
		$setting = $adm->retrieve_setting_from_variable_name($setting_name, MigrationManager :: APPLICATION_NAME);
	   	$setting->set_value($value);
	   	$setting->update();
	   	
	   	$validate_settings[$setting_name] = $value;
	}
	    	
	$setting = $adm->retrieve_setting_from_variable_name('in_migration', MigrationManager :: APPLICATION_NAME);
	$setting->set_value(1);
	$setting->update();
	
	if(!file_exists(dirname(__FILE__) . '/../lib/platform/' . $validate_settings['platform'] . '/'))
	{
		die(Translation :: get('CouldNotFindPlatform'));
	}
	
	$properties = MigrationProperties :: factory($validate_settings['platform']);
    $blocks = $properties->get_migration_blocks();
    
	foreach($blocks as $block)
	{ 
	    if($settings['blocks'][$block] == 1)
	    {
			$migration_block_registration = new MigrationBlockRegistration();
	   		$migration_block_registration->set_name($block);
	    	$migration_block_registration->create();
	    	$selected_blocks[] = $block;
	    }
	}
	
	PlatformSetting :: get_instance()->load_platform_settings();
}
else
{
	$validate_settings = array();
	$selected_blocks = array();
	$migrated_blocks = array(); 
	
	$setting_names = array('platform', 'platform_path', 'move_files', 'migrate_deleted_files');
	foreach($setting_names as $setting_name)
	{
		$validate_settings[$setting_name] = PlatformSetting :: get($setting_name, MigrationManager :: APPLICATION_NAME);
	}
	
	if(!file_exists(dirname(__FILE__) . '/../lib/platform/' . $validate_settings['platform'] . '/'))
	{
		die(Translation :: get('CouldNotFindPlatform'));
	}
	
    $properties = MigrationProperties :: factory($validate_settings['platform']);
	$blocks = $properties->get_migration_blocks();
	
	$migration_block_registrations = MigrationDataManager :: get_instance()->retrieve_migration_block_registrations(null, null, null, new ObjectTableOrder(MigrationBlockRegistration :: PROPERTY_ID));
    while($migration_block_registration = $migration_block_registrations->next_result())
    {
        $block = $migration_block_registration->get_name();
        
    	if($migration_block_registration->get_is_migrated())
    	{
      		$migrated_blocks[] = $block;
	    }
	    else
	    {
        	$selected_blocks[] = $block;
        }
    }
}

$validated = $properties->validate_settings($validate_settings, $selected_blocks, $migrated_blocks);
if($validated)
{
	echo Translation :: get('SettingsValid') . "\n";
}
else
{
	echo Translation :: get('SettingsNotValid') . "\n";
	echo $properties->get_message_logger()->render_for_cli();
	exit;	
}

foreach($selected_blocks as $block)
{
	$migration_block = MigrationBlock :: factory($validate_settings['platform'], $block);
	$migration_block->migrate();
	
	echo $migration_block->get_message_logger()->render_for_cli() . "\n\n";
}

?>