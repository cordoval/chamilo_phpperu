<?php
/**
 * $Id: course_group_manage_subscriptions.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_group.component
 */
require_once dirname(__FILE__) . '/../course_group_tool.class.php';
//require_once dirname(__FILE__) . '/../course_group_tool_component.class.php';
require_once dirname(__FILE__) . '/../../../course_group/course_group_subscriptions_form.class.php';

class CourseGroupToolManageSubscriptionsComponent extends CourseGroupTool
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
        $wdm = WeblcmsDataManager :: get_instance();
        $course_group = $wdm->retrieve_course_group($course_group_id);
        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => CourseGroupTool :: ACTION_UNSUBSCRIBE)), WebLcmsDataManager :: get_instance()->retrieve_course_group(Request :: get(CourseGroupTool :: PARAM_COURSE_GROUP))->get_name()));
        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => CourseGroupTool :: ACTION_MANAGE_SUBSCRIPTIONS, CourseGroupTool :: PARAM_COURSE_GROUP => $course_group_id)), Translation :: get('ManageSubscriptions')));
        $trail->add_help('courses group');
        
        $form = new CourseGroupSubscriptionsForm($course_group, $this->get_url(array(CourseGroupTool :: PARAM_ACTION => CourseGroupTool :: ACTION_MANAGE_SUBSCRIPTIONS, CourseGroupTool :: PARAM_COURSE_GROUP => $course_group->get_id())), $this);
        if ($form->validate())
        {
            $succes = $form->update_course_group_subscriptions();
            
            if ($succes)
                $message = 'CourseGroupSubscriptionsUpdated';
            else
                $message = 'MaximumAmountOfMembersReached';
            
            $this->redirect(Translation :: get($message), ! $succes, array(CourseGroupTool :: PARAM_ACTION => CourseGroupTool :: ACTION_UNSUBSCRIBE, CourseGroupTool :: PARAM_COURSE_GROUP => $course_group->get_id()));
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