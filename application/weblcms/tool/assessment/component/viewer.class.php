<?php
class AssessmentToolViewerComponent extends AssessmentTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function get_content_object_publication_renderer()
    {

    }
    function  add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('AssessmentToolBrowserComponent')));
    }
    function get_additional_parameters()
    {
        return array(Tool :: PARAM_PUBLICATION_ID);
    }
}
?>