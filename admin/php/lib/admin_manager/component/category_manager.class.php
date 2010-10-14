<?php
namespace admin;
use common\libraries\AdministrationComponent;
use common\libraries\DelegateComponent;
/**
 * $Id: category_manager.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.admin_manager.component
 */

/**
 * Weblcms component allows the user to manage course categories
 */
class AdminManagerCategoryManagerComponent extends AdminManager implements AdministrationComponent, DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $category_manager = new AdminCategoryManager($this);
        $category_manager->run();
    }
    
 	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('admin_category_manager');
    }
}
?>