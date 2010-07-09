<?php

class Dokeos185MigrationProperties extends MigrationProperties
{
	function validate_settings()
	{
		
	}
	
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