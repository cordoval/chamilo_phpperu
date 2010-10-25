<?php

/**
 * $Id: request.class.php 224 2010-04-06 14:40:30Z Yannick $
 * @package applicatie.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../../../course/course_request_form.class.php';

class UserToolRequestSubscribeUserComponent extends UserTool
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $failures = 0;

        $trail = BreadcrumbTrail :: get_instance();

        $course = $this->get_course();
        $request = new CourseRequest();
        $form = new CourseRequestForm(CourseRequestForm :: TYPE_CREATE, $this->get_url(), $course, $this, $request, true);

        if ($form->validate())
        {
            $success_request = $form->create_request();
            $this->redirect(Translation :: get($success_request ? 'RequestSent' : 'RequestNotSent'), ($success_request ? false : true), array(Tool :: PARAM_ACTION => UserTool :: ACTION_SUBSCRIBE_USERS));
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
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => UserTool::ACTION_UNSUBSCRIBE_USER_BROWSER)), Translation :: get('UserToolUnsubscribeBrowserComponent')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => UserTool::ACTION_SUBSCRIBE_USER_BROWSER)), Translation :: get('UserToolSubscribeBrowserComponent')));

        $breadcrumbtrail->add_help('weblcms_user_request_subscribe_user');
    }

}

?>