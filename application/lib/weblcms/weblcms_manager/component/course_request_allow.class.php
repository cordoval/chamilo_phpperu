<?php

require_once dirname(__FILE__) . '/../weblcms_manager.class.php';
require_once dirname(__FILE__) . '/admin_request_browser.class.php';
require_once dirname(__FILE__) . '/../../course/course_request_form.class.php';

/**
 * Component to edit an existing request object
 * @author Yannick Meert
 */
class WeblcmsManagerCourseRequestAllowComponent extends WeblcmsManager
{

    private $form;
    private $request_type;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $request_ids = Request :: get(WeblcmsManager :: PARAM_REQUEST);
        $this->request_type = Request :: get(WeblcmsManager:: PARAM_REQUEST_TYPE);
        $failures = 0;

        if ($this->get_user()->is_platform_admin())
        {
            Header :: set_section('admin');
        }

        $trail = BreadcrumbTrail :: get_instance();

        if (!$this->get_user()->is_platform_admin())
        {
            $this->display_header();
            Display :: warning_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }

        $request_method = null;

        switch ($this->request_type)
        {
            case CommonRequest :: SUBSCRIPTION_REQUEST: $request_method = 'retrieve_request';
                break;
            case CommonRequest :: CREATION_REQUEST: $request_method = 'retrieve_course_create_request';
                break;
        }

        $request = $this->$request_method($request_ids[0]);

        if (!is_null($request_ids) && $this->get_user()->is_platform_admin())
        {
            if (!is_array($request_ids))
            {
                $request_ids = array($request_ids);
            }

            foreach ($request_ids as $request_id)
            {
                if (!$this->update_request($request_id))
                {
                    $failures++;
                }
            }
            if ($failures)
            {
                if (count($request_ids) == 1)
                {
                    $message = 'SelectedRequestNotAllowed';
                }
                else
                {
                    $message = 'SelectedRequestsNotAllowed';
                }
            }
            else
            {
                if (count($request_ids) == 1)
                {
                    $message = 'SelectedRequestAllowed';
                }
                else
                {
                    $message = 'SelectedRequestsAllowed';
                }
            }
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_REQUEST_BROWSER, WeblcmsManager :: PARAM_REQUEST => null, WeblcmsManager :: PARAM_REQUEST_TYPE => $this->request_type, WeblcmsManager :: PARAM_REQUEST_VIEW => WeblcmsManagerAdminRequestBrowserComponent :: ALLOWED_REQUEST_VIEW));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoRequestsSelected')));
        }
    }

    function update_request($request_id)
    {

        $request_method = null;

        switch ($this->request_type)
        {
            case CommonRequest :: SUBSCRIPTION_REQUEST: $request_method = 'retrieve_request';
                break;
            case CommonRequest :: CREATION_REQUEST: $request_method = 'retrieve_course_create_request';
                break;
        }

        $wdm = WeblcmsDataManager :: get_instance();
        $request = $wdm->$request_method($request_id);

        if ($this->request_type == CommonRequest :: CREATION_REQUEST)
        {
            $course = new Course();
            $course->set_name($request->get_course_name());
            $course->set_course_type_id($request->get_course_type_id());
            $course->set_titular($request->get_user_id());
            $course->set_visual(strtoupper(uniqid()));

            if (!$course->create())
                return false;

            if (!$this->subscribe_user_to_course($course->get_id(), '1', '1', $request->get_user_id()))
                return false;
        }

        $request->set_decision_date(time());
        $request->set_decision(CommonRequest :: ALLOWED_DECISION);
        return $request->update($request);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {

        if ($this->get_user()->is_platform_admin())
        {
            $breadcrumbtrail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
            $breadcrumbtrail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => WeblcmsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Courses')));
            $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_REQUEST_BROWSER)), Translation :: get('Requests')));
        }
        else
        {
            $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => null)), Translation :: get('Coursetypes')));
        }


        $breadcrumbtrail->add_help('weblcms_course_request_allow');
    }

    function get_additional_parameters()
    {
        return array();
    }


}

?>