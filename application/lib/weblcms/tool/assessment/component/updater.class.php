<?php

class AssessmentToolUpdaterComponent extends AssessmentTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('AssessmentToolBrowserComponent')));
    }
    function get_additional_parameters()
    {
        return array(Tool :: PARAM_PUBLICATION_ID);
    }
}
?>