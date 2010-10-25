<?php

/**
 * $Id: course_type_selector.class.php 218 2010-03-26 14:21:26Z Yannick & Tristan $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';

require_once dirname(__FILE__) . '/../../course_type/course_type_select_form.class.php';

/**
 * Weblcms component allows to select a coursetype
 */
class WeblcmsManagerCourseTypeSelectorComponent extends WeblcmsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {

        if (!WeblcmsDataManager :: get_instance()->count_course_types())
            $this->simple_redirect(array('go' => WeblcmsManager :: ACTION_CREATE_COURSE));

        if ($this->get_user()->is_platform_admin())
        {
            Header :: set_section('admin');
        }

        $trail = BreadcrumbTrail :: get_instance();

        if (!$this->get_user()->is_teacher() && !$this->get_user()->is_platform_admin())
        {
            $this->display_header();
            echo '<div class="clear"></div><br />';
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }

        $course_type_id = $this->get_course_type()->get_id();
        $form = new CourseTypeSelectForm($this->get_url());

        if ($form->validate() || $form->get_size() == 1)
        {
            $this->simple_redirect(array('go' => WeblcmsManager :: ACTION_CREATE_COURSE, 'course_type' => $form->get_selected_id()));
        }
        else
        {
            $this->display_header();
            echo '<div class="clear"></div><br />';
            $form->display();
            $this->display_footer();
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {

        if ($this->get_user()->is_platform_admin())
        {
            $breadcrumbtrail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
            $breadcrumbtrail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => WeblcmsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Courses')));
            $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_TYPE_BROWSER)), Translation :: get('CourseTypeList')));
        }
        $breadcrumbtrail->add_help('weblcms_course_type_selector');
    }

    function get_additional_parameters()
    {
        return array();
    }

}

?>