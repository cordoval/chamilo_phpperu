<?php
/**
 * $Id: course_group_creator.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_group.component
 */
require_once dirname(__FILE__) . '/../course_group_tool.class.php';
//require_once dirname(__FILE__) . '/../course_group_tool_component.class.php';
require_once dirname(__FILE__) . '/../../../course_group/course_group_form.class.php';

class CourseGroupToolCreatorComponent extends CourseGroupTool
{
    private $action_bar;

    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $course_group_id = Request :: get(CourseGroupTool :: PARAM_COURSE_GROUP);
        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => CourseGroupTool :: ACTION_ADD_COURSE_GROUP, CourseGroupTool :: PARAM_COURSE_GROUP => $course_group_id)), Translation :: get('Create')));
        $trail->add_help('courses group');
        
        $course = $this->get_course();
        $course_group = new CourseGroup();
        $course_group->set_course_code($course->get_id());
        $course_group->set_parent_id($course_group_id);
        
        $param_add_course_group[Tool :: PARAM_ACTION] = CourseGroupTool :: ACTION_ADD_COURSE_GROUP;
        $param_add_course_group[CourseGroupTool :: PARAM_COURSE_GROUP] = $course_group_id;
  
        $form = new CourseGroupForm(CourseGroupForm :: TYPE_CREATE, $course_group, $this->get_url($param_add_course_group));
        if ($form->validate())
        {
            $form->create_course_group();
            $this->get_parent()->redirect(Translation :: get('CourseGroupCreated'), false, array('tool_action' => null, CourseGroupTool :: PARAM_COURSE_GROUP => $course_group_id));
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    
    }

}
?>