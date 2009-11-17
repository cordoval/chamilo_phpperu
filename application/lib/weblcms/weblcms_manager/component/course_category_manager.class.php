<?php
/**
 * $Id: course_category_manager.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';
require_once dirname(__FILE__) . '/../weblcms_manager_component.class.php';
require_once dirname(__FILE__) . '/../../course/course_category_form.class.php';
require_once dirname(__FILE__) . '/../../course/course_category_menu.class.php';
require_once dirname(__FILE__) . '/../../category_manager/weblcms_category_manager.class.php';

/**
 * Weblcms component allows the user to manage course categories
 */
class WeblcmsManagerCourseCategoryManagerComponent extends WeblcmsManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $category_manager = new WeblcmsCategoryManager($this);
        $category_manager->run();
    }
}
?>