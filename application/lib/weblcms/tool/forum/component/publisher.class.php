<?php

class ForumToolPublisherComponent extends ForumTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('ForumToolBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(RepoViewer::PARAM_ID, RepoViewer::PARAM_ACTION);
    }
}
?>