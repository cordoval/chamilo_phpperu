<?php
namespace application\weblcms\tool\link;

use application\weblcms\ToolComponent;

class LinkToolCategoryManagerComponent extends LinkTool implements DelegateComponent
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
        return array(CategoryManager::PARAM_CATEGORY_ID);
    }

}

?>