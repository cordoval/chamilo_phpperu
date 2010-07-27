<?php

require_once dirname(__FILE__) . '/course_type_course_list_renderer.class.php';

/**
 * Course list renderer to render the course list with tabs for the course types (used in courses home, courses sorter) and only with open courses
 * @author Sven Vanpoucke
 */
class OpenCourseTypeCourseListRenderer extends CourseTypeCourseListRenderer
{
	/**
	 * The function that is called in the data manager in order to retrieve the courses
	 * This function is splitted from 
	 * @param $condition
	 */
	function retrieve_courses($condition)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_user_courses_with_given_access(CourseSettings :: ACCESS_OPEN, $condition, null, null, new ObjectTableOrder(CourseUserRelation :: PROPERTY_SORT, SORT_ASC, WeblcmsDataManager :: get_instance()->get_alias(CourseUserRelation :: get_table_name())));
	}
}

?>