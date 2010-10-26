<?php
namespace application\weblcms\tool\forum;

use application\weblcms\ToolComponent;
use common\libraries\Translation;

class ForumToolIntroductionPublisherComponent extends ForumTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('ForumToolBrowserComponent')));
    }

}

?>