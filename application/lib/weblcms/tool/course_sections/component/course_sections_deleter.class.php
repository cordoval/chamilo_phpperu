<?php
/**
 * $Id: course_sections_deleter.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_sections.component
 */
require_once dirname(__FILE__) . '/../course_sections_tool.class.php';
require_once dirname(__FILE__) . '/../course_sections_tool_component.class.php';

class CourseSectionsToolDeleterComponent extends CourseSectionsToolComponent
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
        
        $ids = Request :: get(CourseSectionsTool :: PARAM_COURSE_SECTION_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $course_section = new CourseSection();
                $course_section->set_id($id);
                
                if (! $course_section->delete())
                {
                    $failures ++;
                }
                
                $wdm = WeblcmsDataManager :: get_instance();
                $tools = $wdm->get_course_modules(Request :: get('course'));
                
                $conditions[] = new EqualityCondition(CourseSection :: PROPERTY_NAME, Translation :: get('Tools'));
                $conditions[] = new EqualityCondition(CourseSection :: PROPERTY_COURSE_CODE, Request :: get('course'));
                
                $main_section = $wdm->retrieve_course_sections(new AndCondition($conditions))->next_result();
                
                foreach ($tools as $tool)
                {
                    if ($tool->section == $id)
                    {
                        $wdm->change_module_course_section($tool->id, $main_section->get_id());
                    }
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedCourseSectionNotDeleted';
                }
                else
                {
                    $message = 'SelectedCourseSectionsNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedCourseSectionDeleted';
                }
                else
                {
                    $message = 'SelectedCourseSectionsDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures != 0 ? true : false), array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_VIEW_COURSE_SECTIONS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoCourseSectionsSelected')));
        }
    }
}
?>