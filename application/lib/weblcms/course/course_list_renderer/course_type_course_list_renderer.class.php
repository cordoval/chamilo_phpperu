<?php

require_once dirname(__FILE__) . '/course_list_renderer.class.php';

/**
 * Course list renderer to render the course list with tabs for the course types (used in courses home, courses sorter)
 * @author Sven Vanpoucke
 */
class CourseTypeCourseListRenderer extends CourseListRenderer
{
	// The entire course list
	protected $courses;
	
	/**
	 * Returns the course list as html
	 */
	function as_html()
	{
		$this->courses = $this->retrieve_courses();
		return $this->display_course_types();
	}
	
	/**
	 * Retrieves the courses
	 */
	function retrieve_courses()
	{
		$courses = WeblcmsDataManager :: get_instance()->retrieve_all_courses_with_course_categories($this->get_retrieve_courses_condition());
    	return $this->parse_courses($courses);
	}
	
	/**
	 * Returns the conditions needed to retrieve the courses
	 */
	function get_retrieve_courses_condition()
	{
		$access_conditions = array();
    	$access_conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $this->get_parent()->get_user_id(), CourseUserRelation :: get_table_name());
    	$access_conditions[] = new InCondition(CourseGroupRelation :: PROPERTY_GROUP_ID, $this->get_parent()->get_user()->get_groups(true), CourseGroupRelation :: get_table_name());
    	
    	return new OrCondition($access_conditions);
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
			$parsed_courses[$course->get_course_type_id()][$category][] = $course;
		}
	
		return $parsed_courses;
	}
	
	/**
	 * Retrieves the course types
	 */
	function retrieve_course_types()
	{
		return WeblcmsDataManager :: get_instance()->retrieve_active_course_types();
	}
	
	/**
     * Shows the tabs of the course types
     * For each of the tabs show the course list
     */
    function display_course_types()
    {
    	$course_active_types = $this->retrieve_course_types();
    	$renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $course_tabs = new DynamicTabsRenderer($renderer_name);
        
        $index = 0;
        
        while ($course_type = $course_active_types->next_result())
        {
            $course_tabs->add_tab(new DynamicContentTab($index, $course_type->get_name(), null, $this->display_course_user_categories_for_course_type($course_type)));
            $index++;
        }
        
        $course_tabs->add_tab(new DynamicContentTab($index, Translation :: get('NoCourseType'), null, $this->display_course_user_categories_for_course_type()));
        $index++;
        
        return $course_tabs->render();
    }
    
	/**
	 * Retrieves the course user categories for a course type
	 * @param CourseType $course_type
	 */
	function retrieve_course_user_categories_for_course_type(CourseType $course_type)
	{
		if($course_type)
		{
			$course_type_id = $course_type->get_id();
		}
		else
		{
			$course_type_id = 0;
		}
		
		return WeblcmsDataManager :: get_instance()->retrieve_course_user_categories_from_course_type($course_type_id, $this->get_parent()->get_user_id());
	}
    
    /**
	 * Displays the course user categories for a course type
	 * @param CourseType $course_type
	 */
    function display_course_user_categories_for_course_type(CourseType $course_type)
    {
    	$html = array();
    	
    	$html[] = $this->display_course_user_category(null, $course_type);
    	
    	$course_type_user_categories = $this->retrieve_course_user_categories_for_course_type($course_type);
    	
    	$count = 0;
    	$size = $course_type_user_categories->size();
    	
    	while($course_type_user_category = $course_type_user_categories->next_result())
    	{
    		$html[] = $this->display_course_user_category($course_type_user_category, $course_type, $count, $size);
    		$count++;
    	}

    	return implode($html, "\n");
    }
    
    /**
     * Displays the course user category box
     * @param CourseUserCategory $course_user_category
     * @param CourseType $course_type_id
     * @param int $index
     * @param int $count
     */
    function display_course_user_category(CourseTypeUserCategory $course_type_user_category, CourseType $course_type, $offset, $count)
    {
    	$html = array();
    	
    	if (isset($course_type_user_category))
        {
            $title = Utilities :: htmlentities($course_type_user_category->get_optional_property(CourseUserCategory :: PROPERTY_TITLE));
            $course_type_user_category_id = $course_type_user_category->get_id();
        }
        else
        {
            $title = Translation :: get('GeneralCourses');
            $course_type_user_category_id = 0;
        }
        
        $html[] = '<div class="block user_category_block" id="course_user_category_' . $course_type_user_category_id . '">';
        $html[] = '<div class="title user_category_title">';
        $html[] = '<div style="float: left;">' . $title . '</div>';
        $html[] = $this->get_course_type_user_category_actions($course_type_user_category, $course_type, $offset, $count);
        $html[] = '<div style="clear: both;"></div></div>';
        $html[] = '<div class="description user_category_description">';
        
        $html[] = $this->display_courses_for_course_type_user_category($course_type_user_category, $course_type);
        
        $html[] = '</div></div>';
        
        return implode($html, "\n");
    }
	
	/**
	 * Retrieves the courses for a course user category in a given course type
	 * @param CourseUserCategory $course_user_category
	 * @param CourseType $course_type
	 */
	function get_courses_for_course_type_user_category(CourseTypeUserCategory $course_type_user_category, CourseType $course_type)
	{
		$course_type_id = $course_type ? $course_type->get_id() : 0; 
		$course_type_user_category_id = $course_type_user_category ? $course_type_user_category->get_id() : 0;
		return $this->courses[$course_type_id][$course_type_user_category_id];
	}
    
    /**
     * Displays the courses for a user course category
     * @param CourseUserCategory $course_category
     * @param CourseType $course_type_id
     */
    function display_courses_for_course_type_user_category(CourseTypeUserCategory $course_type_user_category, CourseType $course_type)
    {
    	$courses = $this->get_courses_for_course_type_user_category($course_type_user_category, $course_type);
    	$size = count($courses);
    	
    	$html = array();
        
        if ($size > 0)
        {
            $html[] = '<ul>';
            $count = 0;
          	foreach($courses as $course)
            {
                $titular = UserDataManager :: get_instance()->retrieve_user($course->get_titular());
                $html[] = '<div style="float:left;">';
                
                $icon = 'action_home.png';
                $url = $this->get_course_url($course);
                 
                if($course->get_access() == CourseSettings :: ACCESS_CLOSED)
                {
                	$icon = 'action_lock.png';
                	
                	if(!$course->is_course_admin($this->get_user()))
                	{
                		$url = null;
                	}
                }
                
                $html[] = '<li style="list-style: none; margin-bottom: 5px; list-style-image: url(' . Theme :: get_common_image_path() . $icon . ');"><a style="top: -2px; position: relative;" href="' . $url . '">' . $course->get_name() . '</a>';
                
                if($this->get_new_publication_icons() && ($course->get_access() != CourseSettings :: ACCESS_CLOSED || $course->is_course_admin($this->get_user())))
                {
                	$html[] = $this->display_new_publication_icons($course);
                }
                
                $text = array();
                
                if ($course->get_course_code_visible())
                {
                    $text[] = $course->get_visual();
                }
                
                if ($course->get_course_manager_name_visible())
                {
                    $user = UserDataManager :: get_instance()->retrieve_user($course->get_titular());
                    if ($user)
                    {
                        $text[] = $user->get_fullname();
                    }
                    else
                    {
                        $text[] = Translation :: get('NoTitular');
                    }
                }
                
                if ($course->get_course_languages_visible())
                {
                    $text[] = Utilities :: underscores_to_camelcase_with_spaces($course->get_language());
                }
                
                if (count($text) > 0)
                {
                    $html[] = '<br />' . implode(' - ', $text);
                }
                
                $html[] = '</li>';
                $html[] = '</div>';
                $html[] = '<div style="float:right; padding-right: 20px;">';
                $html[] = $this->get_course_actions($course_type_user_category, $course, $course_type, $count, $size);
                $html[] = '</div>';
                $html[] = '<div style="clear: both;"></div>';
                
                $count++;
            }
            $html[] = '</ul>';
        }
        else
        {
            $html[] = '<div class="nocourses"><br />' . Translation :: get('NoCourses') . '</div><br />';
        }
        
        return implode($html, "\n");
    }
}

?>