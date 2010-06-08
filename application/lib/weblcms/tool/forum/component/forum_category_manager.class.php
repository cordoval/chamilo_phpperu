<?php
/**
 * $Id: category_manager.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.component
 */
require_once dirname(__FILE__) . '/../forum_tool.class.php';
require_once dirname(__FILE__) . '/../forum_tool_component.class.php';
require_once dirname(__FILE__) . '/../../../category_manager/content_object_publication_category_manager.class.php';

class ForumToolCategoryManagerComponent extends ForumToolComponent
{
    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $category_manager = new ContentObjectPublicationCategoryManager($this, null, false);
        $category_manager->set_parameter(Tool :: PARAM_ACTION, Tool :: ACTION_MANAGE_CATEGORIES);
        $category_manager->run();
    
    }
}
?>