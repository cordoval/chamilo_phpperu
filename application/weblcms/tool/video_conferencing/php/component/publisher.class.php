<?php
namespace application\weblcms\tool\video_conferencing;

use application\weblcms\Tool;
use common\extensions\repo_viewer\RepoViewer;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use application\weblcms\ToolComponent;
use common\libraries\Translation;

class VideoConferencingToolPublisherComponent extends VideoConferencingTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('VideoConferencingToolBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(RepoViewer :: PARAM_ID, RepoViewer :: PARAM_ACTION);
    }
}
?>