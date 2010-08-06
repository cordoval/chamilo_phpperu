<?php
require_once dirname(__FILE__) . '/../data_class/dokeos185_course_description.class.php';
require_once dirname(__FILE__) . '/../course_data_migration_block.class.php';

class CourseMetaDataMigrationBlock extends CourseDataMigrationBlock
{
	const MIGRATION_BLOCK_NAME = 'course_metadata';
	
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
		return array(new Dokeos185CourseDescription());
	}
}

?>