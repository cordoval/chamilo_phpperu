<?php
/**
 * $Id: course_category_manager.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';

require_once dirname(__FILE__) . '/../../course/course_category_form.class.php';
require_once dirname(__FILE__) . '/../../course/course_category_menu.class.php';
require_once dirname(__FILE__) . '/../../category_manager/weblcms_category_manager.class.php';

/**
 * Weblcms component allows the user to manage course categories
 */
class WeblcmsManagerCourseCategoryManagerComponent extends WeblcmsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $category_manager = new WeblcmsCategoryManager($this);
        $category_manager->run();
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_home_url(), Translation :: get('WeblcmsManagerHomeComponent')));

        if ($this->get_user()->is_platform_admin())
        {
            $breadcrumbtrail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
            $breadcrumbtrail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => WeblcmsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Courses')));
        }
        else
        {
            $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => null)), Translation :: get('Courses')));
        }


    }

    function get_additional_parameters()
    {
    	return array();
    }
}
?>