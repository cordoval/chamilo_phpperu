<?php

class CourseLinksMigrationBlock extends MigrationBlock
{
	const MIGRATION_BLOCK_NAME = 'course_links';
	
	function get_prerequisites()
	{
		return array(CoursesMigrationBlock :: MIGRATION_BLOCK_NAME);
	}
}

?>