<?php

require_once dirname(__FILE__) . '/../../../category_manager/content_object_publication_category_manager.class.php';

class ForumToolCategoryManagerComponent extends ForumTool
{

    function run()
    {
        if (!$this->is_allowed(WeblcmsRights :: VIEW_RIGHT))
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
        return array(CategoryManager::PARAM_CATEGORY_ID);
    }

}

?>