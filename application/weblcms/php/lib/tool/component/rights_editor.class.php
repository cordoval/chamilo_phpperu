<?php

namespace application\weblcms;

use common\libraries\BreadcrumbTrail;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Display;

/**
 * $Id: reporting_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.component
 */
/**
 * Description of reporting_template_viewerclass
 *
 * @author Sven Vanpoucke
 */
require_once dirname(__FILE__) . '/../../courses_rights_editor/courses_rights_editor_manager.class.php';

class ToolComponentRightsEditorComponent extends ToolComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $course = $this->get_course();
        if (!$course->is_course_admin($this->get_user()) && !$this->get_user()->is_platform_admin())
        {
            $this->display_header();
            Display :: error_message(Translation :: get('NotAllowed', null, Utilities:: COMMON_LIBRARIES));
            $this->display_footer();
            exit();
        }
        $trail = BreadcrumbTrail :: get_instance();
        $locations = array();

        $course = $this->get_course_id();
        $course_module = $this->get_tool_id();
        $category_id = Request :: get(WeblcmsManager :: PARAM_CATEGORY);
        $publications = Request :: get(WeblcmsManager :: PARAM_PUBLICATION);
        $this->set_parameter(WeblcmsManager :: PARAM_PUBLICATION, $publications);

        if ($publications)
        {
            $type = WeblcmsRights :: TYPE_PUBLICATION;
            if (!is_array($publications))
            {
                $publications = array($publications);
            }

            foreach ($publications as $publication)
            {
                $locations[] = WeblcmsRights :: get_location_by_identifier_from_courses_subtree(WeblcmsRights :: TYPE_PUBLICATION, $publication, $course);
            }
        }
        else
        {
            if ($category_id)
            {
                $locations[] = WeblcmsRights :: get_location_by_identifier_from_courses_subtree(WeblcmsRights :: TYPE_COURSE_CATEGORY, $category_id, $course);
            }
            else
            {
                if ($course_module && $course_module != 'rights')
                {
                    $course_module = WeblcmsDataManager :: get_instance()->retrieve_course_module_by_name($this->get_course_id(), $this->get_tool_id())->get_id();
                    $locations[] = WeblcmsRights :: get_location_by_identifier_from_courses_subtree(WeblcmsRights :: TYPE_COURSE_MODULE, $course_module, $course);
                }
                else
                {
                    $locations[] = WeblcmsRights :: get_courses_subtree_root($course);
                }
            }
        }

        $manager = new CoursesRightsEditorManager($this, $locations);
        $manager->exclude_users(array($this->get_user_id()));
        $manager->run();
    }

    function get_available_rights()
    {
        return $this->get_parent()->get_available_rights();
    }

    function get_additional_parameters()
    {
        array(WeblcmsManager :: PARAM_PUBLICATION);
    }

}

?>