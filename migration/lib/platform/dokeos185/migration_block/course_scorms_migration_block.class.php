<?php

class CourseScormsMigrationBlock extends MigrationBlock
{
	const MIGRATION_BLOCK_NAME = 'course_scorms';
	
	function get_prerequisites()
	{
		return array(CoursesMigrationBlock :: MIGRATION_BLOCK_NAME);
	}
}

?>