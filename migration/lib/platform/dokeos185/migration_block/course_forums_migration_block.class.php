<?php

class CourseForumsMigrationBlock extends MigrationBlock
{
	const MIGRATION_BLOCK_NAME = 'course_forums';
	
	function get_prerequisites()
	{
		return array(CoursesMigrationBlock :: MIGRATION_BLOCK_NAME);
	}
}

?>