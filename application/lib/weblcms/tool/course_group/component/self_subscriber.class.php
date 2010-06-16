<?php
/**
 * $Id: course_group_self_subscriber.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_group.component
 */
require_once dirname(__FILE__) . '/../course_group_tool.class.php';
//require_once dirname(__FILE__) . '/../course_group_tool_component.class.php';

class CourseGroupToolSelfSubscriberComponent extends CourseGroupTool
{
    private $action_bar;

    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $course_group = $this->get_course_group();
        $course_group->subscribe_users($this->get_user_id());
        $this->redirect(Translation :: get('UserSubscribed'), false, array(Tool :: PARAM_ACTION => CourseGroupTool :: ACTION_UNSUBSCRIBE, CourseGroupTool :: PARAM_COURSE_GROUP => $course_group->get_id()));
    }

}
?>