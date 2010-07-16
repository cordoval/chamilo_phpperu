<?php
/**
 * $Id: import.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager
 */

/**
 * Abstract class to be used by all the tables that are within a course database of dokeos 1.8.5
 * @author Sven Vanpoucke
 */
abstract class Dokeos185CourseDataMigrationDataClass extends Dokeos185MigrationDataClass
{
	/**
	 * The current course for this data
	 * @var Dokeos185Course
	 */
	private $course;

	/**
	 * Sets the course
	 * @param Dokeos185Course $course
	 */
	function set_course($course)
	{
		$this->course = $course;
	}
	
	/**
	 * Retrieves the course
	 */
	function get_course()
	{
		return $this->course;
	}
	
	/**
	 * Sets the database name of this data class which is offcourse the database name of the course
	 */
	function get_database_name()
	{
		return $this->course->get_db_name();
	}
}

?>