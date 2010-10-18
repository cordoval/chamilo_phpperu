<?php

class AnnouncementToolPublisherComponent extends AnnouncementTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(AnnouncementToolPublisherComponent::PARAM_ACTION => AnnouncementToolPublisherComponent::ACTION_BROWSE)), Translation :: get('AnnouncementToolBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(RepoViewer::PARAM_ID, RepoViewer::PARAM_ACTION);
    }

}

?>