<?php

class CourseMetaDataMigrationBlock extends MigrationBlock
{
	const MIGRATION_BLOCK_NAME = 'course_metadata';
	
	function get_prerequisites()
	{
		return array(CoursesMigrationBlock :: MIGRATION_BLOCK_NAME);
	}
}

?>