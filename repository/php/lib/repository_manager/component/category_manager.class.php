<?php
/**
 * $Id: category_manager.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */

/**
 * Weblcms component allows the user to manage course categories
 */
class RepositoryManagerCategoryManagerComponent extends RepositoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $category_manager = new RepositoryCategoryManager($this);
        $category_manager->run();
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('repository_category_manager');
    }
}
?>