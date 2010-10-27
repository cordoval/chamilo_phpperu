<?php
namespace application\weblcms\tool\course_group;

use application\weblcms\CourseGroupForm;
use application\weblcms\WeblcmsDataManager;
use application\weblcms\WeblcmsRights;
use application\weblcms\Tool;
use common\libraries\Display;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Request;
use common\libraries\Translation;

/**
 * $Id: course_group_editor.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_group.component
 */
require_once dirname(__FILE__) . '/../course_group_tool.class.php';
//require_once dirname(__FILE__) . '/../course_group_tool_component.class.php';

class CourseGroupToolEditorComponent extends CourseGroupTool
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

        $wdm = WeblcmsDataManager :: get_instance();
        $course_group = $wdm->retrieve_course_group($course_group_id);

        $form = new CourseGroupForm(CourseGroupForm :: TYPE_EDIT, $course_group, $this->get_url(array(Tool :: PARAM_ACTION => CourseGroupTool :: ACTION_EDIT_COURSE_GROUP, CourseGroupTool :: PARAM_COURSE_GROUP => $course_group_id)));
        if ($form->validate())
        {
            $succes = $form->update_course_group();

            if ($succes)
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
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('CourseGroupToolBrowserComponent')));

        $breadcrumbtrail->add_help('weblcms_course_group_editor');
    }

    function get_additional_parameters()
    {
        return array(CourseGroupTool :: PARAM_COURSE_GROUP);
    }

}

?>