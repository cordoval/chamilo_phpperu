<?php

class CoursesMigrationBlock extends MigrationBlock
{
	const MIGRATION_BLOCK_NAME = 'courses';
	
	function get_prerequisites()
	{
		return array(UsersMigrationBlock :: MIGRATION_BLOCK_NAME);
	}
}

?>