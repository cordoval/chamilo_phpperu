<?php
/**
 * $Id: course_sections_tool_selector.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_sections.component
 */
require_once dirname(__FILE__) . '/../course_sections_tool.class.php';
require_once dirname(__FILE__) . '/../course_sections_tool_component.class.php';
require_once dirname(__FILE__) . '/../course_section_tool_selector_form.class.php';

class CourseSectionsToolToolSelectorComponent extends CourseSectionsToolComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add_help('courses sections');
        
        if (! $this->get_course()->is_course_admin($this->get_parent()->get_user()))
        {
            $this->display_header($trail, true);
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        $id = Request :: get(CourseSectionsTool :: PARAM_COURSE_SECTION_ID);
        if ($id)
        {
            $course_section = WeblcmsDataManager :: get_instance()->retrieve_course_sections(new EqualityCondition('id', $id))->next_result();
            
            $form = new CourseSectionToolSelectorForm($course_section, $this->get_url(array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_SELECT_TOOLS_COURSE_SECTION, CourseSectionsTool :: PARAM_COURSE_SECTION_ID => $id)));
            
            if ($form->validate())
            {
                $success = $form->update_course_modules();
                $this->redirect(Translation :: get($success ? 'CourseSectionUpdated' : 'CourseSectionNotUpdated'), ($success ? false : true), array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_VIEW_COURSE_SECTIONS));
            }
            else
            {
                $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => CourseSectionsTool :: ACTION_VIEW_COURSE_SECTIONS)), $course_section->get_name()));
                $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => CourseSectionsTool :: ACTION_SELECT_TOOLS_COURSE_SECTION, CourseSectionsTool :: PARAM_COURSE_SECTION_ID => $id)), Translation :: get('SelectTools')));
                $this->display_header($trail, true);
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoCourseSectionSelected')));
        }
    }
}
?>