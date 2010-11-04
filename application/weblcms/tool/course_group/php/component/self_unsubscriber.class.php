<?php
namespace application\weblcms\tool\course_group;

use application\weblcms\WeblcmsRights;
use common\libraries\Display;
use common\libraries\Translation;

/**
 * $Id: course_group_self_unsubscriber.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_group.component
 */
require_once dirname(__FILE__) . '/../course_group_tool.class.php';
//require_once dirname(__FILE__) . '/../course_group_tool_component.class.php';


class CourseGroupToolSelfUnsubscriberComponent extends CourseGroupTool
{
    private $action_bar;

    function run()
    {
        $course_group = $this->get_course_group();
        $course_group->unsubscribe_users($this->get_user_id());
        $this->redirect(Translation :: get('UserUnsubscribed'), false, array('tool_action' => null));
    }

}
?>