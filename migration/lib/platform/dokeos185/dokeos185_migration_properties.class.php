<?php

require_once dirname(__FILE__) . '/dokeos185_data_manager.class.php';

class Dokeos185MigrationProperties extends MigrationProperties
{
	/**
	 * Validates the settings of the migration
	 * @param $settings - The general settings
	 * @param $blocks - The selected blocks
	 */
	function validate_settings($settings, $selected_blocks, $migrated_blocks)
	{
		$succes = $this->check_system_availability($settings);
		$succes &= $this->validate_blocks($selected_blocks, $migrated_blocks);
		
		return $succes;
	}
	
	/**
	 * Checks the availability of the system
	 * @param String[] $settings
	 */
	function check_system_availability($settings)
	{
		try
		{
			$data_manager = Dokeos185DataManager::get_instance();
		}
		catch(Exception $e)
		{
			$this->get_message_logger()->add_message($e->getMessage());
			return false;
		}
		
		return true;
	}
	
	/**
	 * Validates every block
	 * Checks if every block his prerequisite is selected as well
	 * @param String[] $blocks - The selected blocks
	 */
	function validate_blocks($selected_blocks, $migrated_blocks)
	{
		if(count($selected_blocks) == 0)
		{
			$this->get_message_logger()->add_message(Translation :: get('NoBlocksSelected'));
			return false;
		}
		
		$blocks = array_merge($selected_blocks, $migrated_blocks);
		
		$result = true;
		
		foreach($selected_blocks as $block)
		{
			$class = Utilities :: underscores_to_camelcase($block) . 'MigrationBlock';
			$object = new $class();
			if(!$object->check_prerequisites($blocks))
			{
				$result = false;
				$this->get_message_logger()->add_message(Translation :: get('BlockPrerequisitesCheckFailed', array('BLOCK' => Utilities :: underscores_to_camelcase($block))));
			}
		}
		
		return $result;
	}
	
	/**
	 * We need to define this manually because the order of the migration blocks is of very big importance for the prerequisites of some blocks
	 */
	function get_migration_blocks()
	{
		$this->require_migration_blocks();
		
		$blocks = array(UsersMigrationBlock :: MIGRATION_BLOCK_NAME, ClassesMigrationBlock :: MIGRATION_BLOCK_NAME, PersonalAgendasMigrationBlock :: MIGRATION_BLOCK_NAME, 
					    SettingsMigrationBlock :: MIGRATION_BLOCK_NAME, CoursesMigrationBlock :: MIGRATION_BLOCK_NAME, CourseAnnouncementsMigrationBlock :: MIGRATION_BLOCK_NAME,
					    CourseAssignmentsMigrationBlock :: MIGRATION_BLOCK_NAME, CourseCalendarEventsMigrationBlock :: MIGRATION_BLOCK_NAME, CourseDocumentsMigrationBlock :: MIGRATION_BLOCK_NAME,
					    CourseDropboxesMigrationBlock :: MIGRATION_BLOCK_NAME, CourseForumsMigrationBlock :: MIGRATION_BLOCK_NAME, CourseGroupsMigrationBlock :: MIGRATION_BLOCK_NAME,
					    CourseLearningPathsMigrationBlock :: MIGRATION_BLOCK_NAME, CourseLinksMigrationBlock :: MIGRATION_BLOCK_NAME, CourseMetaDataMigrationBlock :: MIGRATION_BLOCK_NAME,
					    CourseQuizzesMigrationBlock :: MIGRATION_BLOCK_NAME, CourseScormsMigrationBlock :: MIGRATION_BLOCK_NAME, CourseStudentPublicationsMigrationBlock :: MIGRATION_BLOCK_NAME,
					    CourseSurveysMigrationBlock :: MIGRATION_BLOCK_NAME, TrackersMigrationBlock :: MIGRATION_BLOCK_NAME);

		return $blocks;
	}
	
	function require_migration_blocks()
	{
		$dir = dirname(__FILE__) . '/migration_block/';
    	
    	if(!file_exists($dir))
    	{
    		return;
    	}
    	
    	$files = Filesystem :: get_directory_content($dir, Filesystem :: LIST_FILES, false);
    	
    	foreach($files as $file)
    	{
    		require_once($dir . $file);
    	}
	}
}