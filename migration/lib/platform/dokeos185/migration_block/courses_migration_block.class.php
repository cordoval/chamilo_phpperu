<?php

require_once dirname(__FILE__) . '/../data_class/dokeos185_course.class.php';
require_once dirname(__FILE__) . '/../data_class/dokeos185_course_category.class.php';
require_once dirname(__FILE__) . '/../data_class/dokeos185_user_course_category.class.php';
require_once dirname(__FILE__) . '/../data_class/dokeos185_course_rel_user.class.php';
require_once dirname(__FILE__) . '/../data_class/dokeos185_course_rel_class.class.php';

/**
 * Class to start the migration of the courses, course categories, course user categories, course user relations and course classes
 * @author vanpouckesven
 *
 */
class CoursesMigrationBlock extends MigrationBlock
{
	const MIGRATION_BLOCK_NAME = 'courses';
	
	function get_prerequisites()
	{
		return array(UsersMigrationBlock :: MIGRATION_BLOCK_NAME, ClassesMigrationBlock:: MIGRATION_BLOCK_NAME);
	}
	
	function get_data_classes()
	{
		return array(new Dokeos185CourseCategory(), new Dokeos185Course(), new Dokeos185UserCourseCategory(), new Dokeos185CourseRelUser(), new Dokeos185CourseRelClass());
	}
	
	function get_block_name()
	{
		return self :: MIGRATION_BLOCK_NAME;
	}
}

?>