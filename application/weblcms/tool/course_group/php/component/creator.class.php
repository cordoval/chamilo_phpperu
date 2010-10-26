<?php
namespace application\weblcms\tool\course_group;

use common\libraries\Display;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Request;
use common\libraries\Translation;

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
        if (! $this->is_allowed(WeblcmsRights :: VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }

        $course_group_id = Request :: get(CourseGroupTool :: PARAM_COURSE_GROUP);

        $trail = BreadcrumbTrail :: get_instance();

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

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('CourseGroupToolBrowserComponent')));

        $breadcrumbtrail->add_help('weblcms_course_group_creator');
    }

}

?>