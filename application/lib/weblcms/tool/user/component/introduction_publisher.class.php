<?php

class UserToolIntroductionPublisherComponent extends UserTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => UserTool :: ACTION_UNSUBSCRIBE_USER_BROWSER)), Translation :: get('UserToolUnsubscribeUserBrowserComponent')));
    }
}
?>