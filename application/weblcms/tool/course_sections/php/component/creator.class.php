<?php
namespace application\weblcms\tool\course_sections;

use application\weblcms\Tool;
use common\libraries\Display;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * $Id: course_sections_creator.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_sections.component
 */
require_once dirname(__FILE__) . '/../course_sections_tool.class.php';
require_once dirname(__FILE__) . '/../course_section_form.class.php';

class CourseSectionsToolCreatorComponent extends CourseSectionsTool
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('courses sections');
        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => CourseSectionsTool :: ACTION_CREATE_COURSE_SECTION)), Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES)));

        if (! $this->get_course()->is_course_admin($this->get_parent()->get_user()))
        {
            $this->display_header();
            Display :: error_message(Translation :: get('NotAllowed', null, Utilities :: COMMON_LIBRARIES));
            $this->display_footer();
            exit();
        }

        $course_section = new CourseSection();
        $course_section->set_course_code($this->get_course_id());
        $course_section->set_type(CourseSection :: TYPE_TOOL);

        $form = new CourseSectionForm(CourseSectionForm :: TYPE_CREATE, $course_section, $this->get_url(array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_CREATE_COURSE_SECTION)));

        if ($form->validate())
        {
            $success = $form->create_course_section();
            if ($success)
            {
                $course_section = $form->get_course_section();
                $this->redirect(Translation :: get('ObjectCreated', array('OBJECT' => Translation::get('CourseSection')),Utilities:: COMMON_LIBRARIES ), (false), array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_VIEW_COURSE_SECTIONS));
            }
            else
            {
                $this->redirect(Translation :: get('ObjectNotCreated', array('OBJECT' => Translation::get('CourseSection')),Utilities:: COMMON_LIBRARIES ), (true), array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_VIEW_COURSE_SECTIONS));
            }
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