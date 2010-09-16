<?php

/**
 * $Id: reporting.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */
class WeblcmsManagerReportingComponent extends WeblcmsManager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $template = Request :: get(ReportingManager :: PARAM_TEMPLATE_ID);

        $params[ReportingManager::PARAM_TEMPLATE_ID] = $template;

        $trail = BreadcrumbTrail :: get_instance();

        $rtv = ReportingViewer :: construct($this);
        if (isset($template))
        {
            $rtv->add_template_by_id($template);
        }
        else
        {
            $rtv->add_template_by_name('course_student_tracker_reporting_template', WeblcmsManager::APPLICATION_NAME);
        }
        $rtv->set_breadcrumb_trail($trail);
        $rtv->show_all_blocks();

        $rtv->run();
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        if ($this->get_user()->is_platform_admin())
        {
            $breadcrumbtrail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
            $breadcrumbtrail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => WeblcmsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Courses')));
        }


        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_BROWSER, WeblcmsManager :: PARAM_COURSE => null)), Translation :: get('CourseList')));

//        if ($breadcrumbtrail->get_last() != new Breadcrumb($this->get_reporting_url($params), Translation :: get('Reporting')))
//        {
//            $breadcrumbtrail->add(new Breadcrumb($this->get_reporting_url($params), Translation :: get('Reporting')));
//        }
    }

    function get_additional_parameters()
    {
        return array(ReportingManager :: PARAM_TEMPLATE_ID);
    }

}

?>