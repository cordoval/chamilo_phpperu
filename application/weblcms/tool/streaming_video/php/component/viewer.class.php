<?php
namespace application\weblcms\tool\streaming_video;


class StreamingVideoToolViewerComponent extends StreamingVideoTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('StreamingVideoToolBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(Tool :: PARAM_PUBLICATION_ID);
    }

}

?>