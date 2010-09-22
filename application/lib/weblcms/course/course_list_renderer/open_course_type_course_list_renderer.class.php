<?php

require_once dirname(__FILE__) . '/course_type_course_list_renderer.class.php';

/**
 * Course list renderer to render the course list with tabs for the course types (used in courses home, courses sorter) and only with open courses
 * @author Sven Vanpoucke
 */
class OpenCourseTypeCourseListRenderer extends CourseTypeCourseListRenderer
{
	/**
	 * Returns the conditions needed to retrieve the courses
	 */
	function get_retrieve_courses_condition()
	{
		$conditions = array();
		$conditions[] = parent :: get_retrieve_courses_condition();
		$conditions[] = new EqualityCondition(CourseSettings :: PROPERTY_ACCESS, CourseSettings :: ACCESS_OPEN, CourseSettings :: get_table_name());
    	
    	return new AndCondition($conditions);
	}
}

?>