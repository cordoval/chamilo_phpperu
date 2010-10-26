<?php
namespace application\weblcms\tool\wiki;

use application\weblcms\ToolComponent;
use common\libraries\Translation;

class WikiToolPublisherComponent extends WikiTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('WikiToolBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(RepoViewer :: PARAM_ID, RepoViewer :: PARAM_ACTION);
    }

}

?>