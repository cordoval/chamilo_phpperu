<?php

/**
 * $Id: request.class.php 224 2010-04-06 14:40:30Z Yannick $
 * @package applicatie.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';
require_once dirname(__FILE__) . '/../../course/course_create_request.class.php';
require_once dirname(__FILE__) . '/../../course/course_request_form.class.php';

class WeblcmsManagerCourseCreateRequestCreatorComponent extends WeblcmsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $course_code = Request :: get(WeblcmsManager :: PARAM_COURSE);
        $course = $this->retrieve_course($course_code);
        $failures = 0;

        $trail = BreadcrumbTrail :: get_instance();


        $request = new CourseCreateRequest();
        $form = new CourseRequestForm(CourseRequestForm :: TYPE_CREATE, $this->get_url(array(WeblcmsManager :: PARAM_COURSE => $course_code)), $course, $this, $request);

        if ($form->validate())
        {
            $success_request = $form->create_request();
            $array_type = array();
            $array_type['go'] = WeblcmsManager :: ACTION_VIEW_WEBLCMS_HOME;
            $this->redirect(Translation :: get($success_request ? 'CourseCreateRequestSent' : 'CourseCreateRequestNotSent'), ($success_request ? false : true), $array_type, array(WeblcmsManager :: PARAM_COURSE));
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
        $breadcrumbtrail->add(new Breadcrumb($this->get_home_url(), Translation :: get('WeblcmsManagerHomeComponent')));

        $breadcrumbtrail->add(new Breadcrumb($this->get_url(null, array(Application :: PARAM_ACTION)), Translation :: get('MyCourses')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(), Translation :: get('Create')));
        $breadcrumbtrail->add_help('course create request');
    }

    function get_additional_parameters()
    {
        return array();
    }

}

?>