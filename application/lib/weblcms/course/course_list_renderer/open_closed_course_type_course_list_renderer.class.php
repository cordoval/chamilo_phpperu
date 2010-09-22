<?php

require_once dirname(__FILE__) . '/course_type_course_list_renderer.class.php';

/**
 * Course list renderer to render the course list with tabs for the course types (used in courses home, courses sorter) and with open and closed courses splitted
 * @author Sven Vanpoucke
 */
class OpenClosedCourseTypeCourseListRenderer extends CourseTypeCourseListRenderer
{
	private $current_access_state;
	
	/**
	 * Displays the course user categories for a course type
	 * @param CourseType $course_type
	 */
    function display_course_user_categories_for_course_type(CourseType $course_type)
    {
    	$html = array();
    	
    	foreach(CourseSettings :: get_access_states() as $access_state => $translation)
    	{
    		$html[] = '<h3>' . $translation . '</h3>';
    		$this->current_access_state = $access_state;
    		$html[] = parent :: display_course_user_categories_for_course_type($course_type);
    	}	
    	
    	return implode($html, "\n");
    }

	/**
	 * Parsers the courses in a structure in course type / course category
	 * @param Course[] $courses
	 */
	function parse_courses($courses)
	{
		$parsed_courses = array();
		
		while($course = $courses->next_result())
		{
			$category = $course->get_optional_property(CourseTypeUserCategoryRelCourse :: PROPERTY_COURSE_TYPE_USER_CATEGORY_ID) ? $course->get_optional_property(CourseTypeUserCategoryRelCourse :: PROPERTY_COURSE_TYPE_USER_CATEGORY_ID) : 0;
			$access = $course->get_optional_property(CourseSettings :: PROPERTY_ACCESS);
			$parsed_courses[$course->get_course_type_id()][$category][$access][] = $course;
		}

		return $parsed_courses;
	}
	
	/**
	 * Retrieves the courses for a course user category in a given course type
	 * @param CourseUserCategory $course_user_category
	 * @param CourseType $course_type
	 */
	function get_courses_for_course_user_category(CourseUserCategory $course_user_category, CourseType $course_type)
	{
		$course_type_id = $course_type ? $course_type->get_id() : 0; 
		$course_user_category_id = $course_user_category ? $course_user_category->get_id() : 0;
		return $this->courses[$course_type_id][$course_user_category_id][$this->current_access_state];
	}
}

?>