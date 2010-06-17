<?php
/**
 * $Id: course_sections_mover.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_sections.component
 */
require_once dirname(__FILE__) . '/../course_sections_tool.class.php';

class CourseSectionsToolMoverComponent extends CourseSectionsTool
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $user = $this->get_user();
        
        if (! $this->get_course()->is_course_admin($this->get_parent()->get_user()))
        {
            $trail = new BreadcrumbTrail();
            $trail->add_help('courses sections');
            $this->display_header($trail, true);
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        $id = Request :: get(CourseSectionsTool :: PARAM_COURSE_SECTION_ID);
        $direction = Request :: get(CourseSectionsTool :: PARAM_DIRECTION);
        $failures = 0;
        
        if (! empty($id))
        {
            $course_section = WeblcmsDataManager :: get_instance()->retrieve_course_sections(new EqualityCondition('id', $id))->next_result();
            
            $display_order = $course_section->get_display_order();
            $new_place = $display_order + $direction;
            $course_section->set_display_order($new_place);
            
            $conditions[] = new EqualityCondition(CourseSection :: PROPERTY_DISPLAY_ORDER, $new_place);
            $conditions[] = new EqualityCondition(CourseSection :: PROPERTY_COURSE_CODE, $this->get_course_id());
            $condition = new AndCondition($conditions);
            $new_course_section = WeblcmsDataManager :: get_instance()->retrieve_course_sections($condition)->next_result();
            $new_course_section->set_display_order($display_order);
            
            $success = $course_section->update() & $new_course_section->update();
            
            $message = $success ? 'CourseSectionMoved' : 'CourseSectionNotMoved';
            
            $this->redirect(Translation :: get($message), (! $success), array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_VIEW_COURSE_SECTIONS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoCourseSectionsSelected')));
        }
    }
}
?>