<?php
/**
 * $Id: course_group_editor.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_group.component
 */
require_once dirname(__FILE__) . '/../course_group_tool.class.php';
//require_once dirname(__FILE__) . '/../course_group_tool_component.class.php';
require_once dirname(__FILE__) . '/../../../course_group/course_group_form.class.php';

class CourseGroupToolEditorComponent extends CourseGroupTool
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
        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => CourseGroupTool :: ACTION_UNSUBSCRIBE)), WebLcmsDataManager :: get_instance()->retrieve_course_group(Request :: get(CourseGroupTool :: PARAM_COURSE_GROUP))->get_name()));
        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => CourseGroupTool :: ACTION_EDIT_COURSE_GROUP, CourseGroupTool :: PARAM_COURSE_GROUP => $course_group_id)), Translation :: get('Edit')));
        $trail->add_help('courses group');
        
        $wdm = WeblcmsDataManager :: get_instance();
        $course_group = $wdm->retrieve_course_group($course_group_id);
        
        $form = new CourseGroupForm(CourseGroupForm :: TYPE_EDIT, $course_group, $this->get_url(array(Tool :: PARAM_ACTION => CourseGroupTool :: ACTION_EDIT_COURSE_GROUP, CourseGroupTool :: PARAM_COURSE_GROUP => $course_group_id)));
        if ($form->validate())
        {
            $succes = $form->update_course_group();
            
            if($succes)
            {
            	$message = Translation :: get('CourseGroupUpdated');
            }
            else
            {
            	$message = Translation :: get('CourseGroupNotUpdated') . '<br />' . implode('<br />', $course_group->get_errors());
            }
            
            $this->redirect($message, ! $succes, array(Tool :: PARAM_ACTION => CourseGroupTool :: ACTION_VIEW_GROUPS, CourseGroupTool :: PARAM_COURSE_GROUP => $course_group->get_parent_id()));
        }
        else
        {
            $this->display_header($trail, true);
            $form->display();
            $this->display_footer();
        }
    
    }

}
?>