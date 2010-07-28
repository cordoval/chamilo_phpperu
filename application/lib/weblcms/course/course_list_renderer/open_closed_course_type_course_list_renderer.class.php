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
	 * The function that is called in the data manager in order to retrieve the courses
	 * This function is splitted from 
	 * @param $condition
	 */
	function retrieve_courses($condition)
	{
		return WeblcmsDataManager :: get_instance()->retrieve_user_courses_with_given_access($this->current_access_state, $condition, null, null, new ObjectTableOrder(CourseUserRelation :: PROPERTY_SORT, SORT_ASC, WeblcmsDataManager :: get_instance()->get_alias(CourseUserRelation :: get_table_name())));
	}
}

?>