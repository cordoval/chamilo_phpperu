<?php
/**
 * $Id: user_details.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.user.component
 */
require_once dirname(__FILE__) . '/../user_tool.class.php';

class UserToolReportingViewerComponent extends UserTool implements DelegateComponent
{

    function run()
    {
        $template = Utilities::camelcase_to_underscores('CourseStudentTrackerDetailReportingTemplate');
        $rtv = ReportingViewer :: construct($this);
        $rtv->add_template_by_name($template, WeblcmsManager::APPLICATION_NAME);
		$rtv->show_all_blocks();
        $rtv->run();
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => UserTool :: ACTION_UNSUBSCRIBE_USER_BROWSER)), Translation :: get('UserToolUnsubscribeUserBrowserComponent')));

        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => UserTool :: ACTION_USER_DETAILS, UserTool :: PARAM_USERS => Request::get(UserTool :: PARAM_USERS))), Translation :: get('UserToolDetailsComponent')));
    }

    function get_additional_parameters()
    {
        return array(Tool :: PARAM_PUBLICATION_ID, Tool :: PARAM_COMPLEX_ID, Tool :: PARAM_TEMPLATE_NAME);
    }

}
?>