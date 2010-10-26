<?php
namespace application\weblcms\tool\blog;

use application\weblcms\ToolComponent;
use common\libraries\Translation;

class BlogToolIntroductionPublisherComponent extends BlogTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('BlogToolBrowserComponent')));
    }

}
?>