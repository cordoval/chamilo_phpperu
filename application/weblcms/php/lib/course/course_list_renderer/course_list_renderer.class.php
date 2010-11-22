<?php
namespace application\weblcms;

use common\libraries\Theme;
use common\libraries\OrCondition;
use common\libraries\InCondition;
use common\libraries\EqualityCondition;
use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * Course list renderer to render the course list (used in courses home, courses sorter, courses block...)
 * @author Sven Vanpoucke
 */
class CourseListRenderer
{
    /**
     * The parent on which the course list renderer is running
     */
    private $parent;

    /**
     * Show the what's new icons or not
     * @var boolean
     */
    private $new_publication_icons;

    function __construct($parent)
    {
        $this->parent = $parent;
        $this->new_publication_icons = false;
    }

    function get_parent()
    {
        return $this->parent;
    }

    function set_parent($parent)
    {
        $this->parent = $parent;
    }

    function get_user()
    {
        return $this->get_parent()->get_user();
    }

    function show_new_publication_icons()
    {
        $this->new_publication_icons = true;
    }

    function get_new_publication_icons()
    {
        return $this->new_publication_icons;
    }

    /**
     * Retrieves the actions for the given course user category
     * @param CourseUserCategory $course_user_category
     * @param CourseType $course_type
     */
    function get_course_type_user_category_actions(CourseTypeUserCategory $course_type_user_category, CourseType $course_type, $offset, $count)
    {
        if (method_exists($this->get_parent(), 'get_course_type_user_category_actions'))
        {
            $course_type_id = 0;
            if ($course_type)
            {
                $course_type_id = $course_type->get_id();
            }

            return $this->get_parent()->get_course_type_user_category_actions($course_type_user_category, $course_type_id, $offset, $count);
        }
    }

    /**
     * Retrieves the actions for the given course
     * @param Course $course
     */
    function get_course_actions(CourseTypeUserCategory $course_type_user_category, Course $course, CourseType $course_type, $offset, $count)
    {
        if (method_exists($this->get_parent(), 'get_course_actions'))
        {
            $course_type_id = 0;
            if ($course_type)
            {
                $course_type_id = $course_type->get_id();
            }

            return $this->get_parent()->get_course_actions($course_type_user_category, $course, $course_type_id, $offset, $count);
        }
    }

    /**
     * Renders the course list
     */
    function render()
    {
        echo $this->as_html();
    }

    /**
     * Returns the course list as html
     */
    function as_html()
    {
        return $this->display_courses();
    }

    /**
     * Retrieves the courses for the user
     */
    function retrieve_courses()
    {
        $access_conditions = array();
        $access_conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $this->get_parent()->get_user_id(), CourseUserRelation :: get_table_name());
        $access_conditions[] = new InCondition(CourseGroupRelation :: PROPERTY_GROUP_ID, $this->get_parent()->get_user()->get_groups(true), CourseGroupRelation :: get_table_name());

        $condition = new OrCondition($access_conditions);
        return WeblcmsDataManager :: get_instance()->retrieve_user_courses($condition);
    }

    /**
     * Displays the courses
     */
    function display_courses()
    {
        $html = array();
        $courses = $this->retrieve_courses();

        if ($courses->size() > 0)
        {
            $html[] = '<ul style="padding: 0px; margin: 0px 0px 0px 15px;">';
            while ($course = $courses->next_result())
            {
                if ($course->get_access() == CourseSettings :: ACCESS_CLOSED && ! $course->is_course_admin($this->get_user()))
                {
                    continue;
                }

                $html[] = '<li><a href="' . $this->get_course_url($course) . '">' . $course->get_name() . '</a>';

                if ($this->get_new_publication_icons())
                {
                    $html[] = $this->display_new_publication_icons($course);
                }

                $html[] = '</li>';

            }
            $html[] = '</ul>';
        }
        return implode($html, "\n");
    }

    /**
     * Displays the what's new icons
     * @param Course $course
     */
    function display_new_publication_icons(Course $course)
    {
        $tools = WeblcmsDataManager :: get_instance()->get_course_modules($course->get_id());
        $html = array();

        foreach ($tools as $index => $tool)
        {
            if ($tool->visible && WeblcmsDataManager :: tool_has_new_publications($tool->name, $this->get_user(), $course))
            {
                $html[] = '<a href="' . $this->get_tool_url($tool->name, $course) . '"><img src="' . Theme :: get_image_path('weblcms') . 'tool_' . $tool->name . '_new.png" alt="' . Translation :: get('New', null, Utilities :: COMMON_LIBRARIES ) . '"/></a>';
            }
        }
        return implode($html, "\n");
    }

    /**
     * Gets the url from the given course
     * @param Course $course
     */
    function get_course_url(Course $course)
    {
        $parameters = array();
        $parameters[WeblcmsManager :: PARAM_APPLICATION] = WeblcmsManager :: APPLICATION_NAME;
        $parameters[WeblcmsManager :: PARAM_ACTION] = WeblcmsManager :: ACTION_VIEW_COURSE;
        $parameters[WeblcmsManager :: PARAM_COURSE] = $course->get_id();
        return $this->get_parent()->get_link($parameters);
    }

    /**
     * Gets the url from the given tool in the given course
     * @param String $tool
     * @param Course $course
     */
    function get_tool_url($tool, Course $course)
    {
        $parameters = array();
        $parameters[WeblcmsManager :: PARAM_APPLICATION] = WeblcmsManager :: APPLICATION_NAME;
        $parameters[WeblcmsManager :: PARAM_ACTION] = WeblcmsManager :: ACTION_VIEW_COURSE;
        $parameters[WeblcmsManager :: PARAM_COURSE] = $course->get_id();
        $parameters[WeblcmsManager :: PARAM_TOOL] = $tool;
        return $this->get_parent()->get_link($parameters);
    }
}

?>