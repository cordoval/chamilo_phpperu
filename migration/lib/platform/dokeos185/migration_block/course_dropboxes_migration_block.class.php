<?php
require_once dirname(__FILE__) . '/../course_data_migration_block.class.php';
require_once dirname(__FILE__) . '/../data_class/dokeos185_dropbox_category.class.php';
require_once dirname(__FILE__) . '/../data_class/dokeos185_dropbox_file.class.php';

class CourseDropboxesMigrationBlock extends CourseDataMigrationBlock
{
	const MIGRATION_BLOCK_NAME = 'course_dropboxes';
	
	function get_prerequisites()
	{
		return array(CoursesMigrationBlock :: MIGRATION_BLOCK_NAME);
	}
	
	function get_block_name()
	{
		return self :: MIGRATION_BLOCK_NAME;
	}
	
	function get_course_data_classes()
	{
		return array(new Dokeos185DropboxCategory(), new Dokeos185DropboxFile());
	}
}

?>