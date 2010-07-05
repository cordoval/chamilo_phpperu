<?php
/**
 * $Id: category_manager.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.component
 */
require_once dirname(__FILE__) . '/../../category_manager/content_object_publication_category_manager.class.php';

class ToolCategoryManagerComponent extends ToolComponent
{
    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $category_manager = new ContentObjectPublicationCategoryManager($this);
        $category_manager->run();
    
    }
}
?>