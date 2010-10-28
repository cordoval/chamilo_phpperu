<?php
namespace application\weblcms\tool\learning_path;

use common\extensions\category_manager\CategoryManager;
use application\weblcms\Tool;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use application\weblcms\ToolComponent;
use common\libraries\DelegateComponent;
use common\libraries\Translation;

class LearningPathToolCategoryManagerComponent extends LearningPathTool implements DelegateComponent
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('LearningPathToolBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(CategoryManager :: PARAM_CATEGORY_ID);
    }

}

?>