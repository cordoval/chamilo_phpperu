<?php

class CourseMetaDataMigrationBlock extends MigrationBlock
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
	
	function get_data_classes()
	{
		return array();
	}
}

?>