<?php
/**
 * $Id: course_category_browser_table_column_model.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component.course_category_browser
 */
require_once dirname(__FILE__) . '/../../../course/course_category_table/default_course_category_table_column_model.class.php';
/**
 * Table column model for the coursecategory browser table
 */
class CourseCategoryBrowserTableColumnModel extends DefaultCourseCategoryTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function CourseCategoryBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->set_default_order_column(0);
        $this->add_column(self :: get_modification_column());
    }

    /**
     * Gets the modification column
     * @return CourseCategoryTableColumn
     */
    static function get_modification_column()
    {
        if (! isset(self :: $modification_column))
        {
            self :: $modification_column = new StaticTableColumn('');
        }
        return self :: $modification_column;
    }
}
?>
