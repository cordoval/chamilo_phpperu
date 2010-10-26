<?php
namespace application\weblcms;


/**
 * $Id: course_type_deleter.class.php 218 2010-03-15 10:30:26Z Yannick $
 * @package application.lib.weblcms.weblcms_manager.component
 */

/**
 * Repository manager component which provides functionality to delete a course_type
 */
class WeblcmsManagerCourseTypeCoursesDeleterComponent extends WeblcmsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $course_type_id = Request :: get(WeblcmsManager :: PARAM_COURSE_TYPE);
        $failures = 0;

        if (!$this->get_user()->is_platform_admin())
        {
            $trail = BreadcrumbTrail :: get_instance();

            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }

        if (!empty($course_type_id))
        {

            $wdm = WeblcmsDataManager::get_instance();
            $result = $wdm->delete_courses_by_course_type_id($course_type_id);
            if ($result)
                $message = 'AllSelectedCoursesDeleted';
            else
                $message = 'NotAllSelectedCoursesDeleted';

            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_COURSE_TYPE, WeblcmsManager :: PARAM_COURSE_TYPE => $course_type_id));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoCourseTypeSelected')));
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_course_type_courses_deleter');

    }

    function get_additional_parameters()
    {
        return array();
    }

}

?>