<?php
namespace application\weblcms\tool\forum;

use application\weblcms\WeblcmsRights;
use common\extensions\category_manager\CategoryManager;
use application\weblcms\ContentObjectPublicationCategoryManager;
use application\weblcms\Tool;
use common\libraries\Display;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\DelegateComponent;
use common\libraries\Translation;

class ForumToolCategoryManagerComponent extends ForumTool implements DelegateComponent
{

    function run()
    {
        if (! $this->is_allowed(WeblcmsRights :: VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }

        $category_manager = new ContentObjectPublicationCategoryManager($this, null, false);
        $category_manager->run();
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('ForumToolBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(CategoryManager :: PARAM_CATEGORY_ID);
    }

}

?>