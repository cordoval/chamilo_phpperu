<?php
namespace application\weblcms\tool\document;

use application\weblcms\ToolComponent;
use common\libraries\DelegateComponent;
use common\libraries\Translation;

class DocumentToolCategoryManagerComponent extends DocumentTool implements DelegateComponent
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('DocumentToolBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(CategoryManager :: PARAM_CATEGORY_ID);
    }

}
?>
