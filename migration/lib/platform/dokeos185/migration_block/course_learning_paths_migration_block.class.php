<?php

class CourseLearningPathsMigrationBlock extends MigrationBlock
{
	const MIGRATION_BLOCK_NAME = 'course_learning_paths';
	
	function get_prerequisites()
	{
		return array(CoursesMigrationBlock :: MIGRATION_BLOCK_NAME);
	}
}

?>