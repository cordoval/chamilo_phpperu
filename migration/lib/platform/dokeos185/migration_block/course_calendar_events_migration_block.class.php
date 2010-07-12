<?php

class CourseCalendarEventsMigrationBlock extends MigrationBlock
{
	const MIGRATION_BLOCK_NAME = 'course_calendar_events';
	
	function get_prerequisites()
	{
		return array(CoursesMigrationBlock :: MIGRATION_BLOCK_NAME);
	}
}

?>