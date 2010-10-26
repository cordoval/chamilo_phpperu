<?php
namespace application\weblcms\tool\link;

use application\weblcms\ToolComponent;
use common\libraries\Translation;

class LinkToolPublisherComponent extends LinkTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('LinkToolBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(RepoViewer :: PARAM_ID, RepoViewer :: PARAM_ACTION);
    }

}

?>