<?php
/**
 * $Id: category_manager.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */

/**
 * Weblcms component allows the user to manage course categories
 */
class RepositoryManagerCategoryManagerComponent extends RepositoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail(false);
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ManageCategories')));
        
        $category_manager = new RepositoryCategoryManager($this, $trail);
        $category_manager->run();
    }
}
?>