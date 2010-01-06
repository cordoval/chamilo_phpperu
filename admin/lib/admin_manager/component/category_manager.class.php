<?php
/**
 * $Id: category_manager.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.admin_manager.component
 */

/**
 * Weblcms component allows the user to manage course categories
 */
class AdminManagerCategoryManagerComponent extends AdminManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(AdminManager :: PARAM_ACTION => null)), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => AdminManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Admin')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ManageCategories')));
        $category_manager = new AdminCategoryManager($this, $trail);
        $category_manager->run();
    }
}
?>