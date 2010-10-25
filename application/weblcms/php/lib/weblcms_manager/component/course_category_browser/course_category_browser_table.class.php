<?php
/**
 * $Id: course_category_browser_table.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component.course_category_browser
 */
require_once dirname(__FILE__) . '/course_category_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/course_category_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/course_category_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../weblcms_manager.class.php';
/**
 * Table to display a set of course categories.
 */
class CourseCategoryBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'course_category_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function CourseCategoryBrowserTable($browser, $parameters, $condition)
    {
        $model = new CourseCategoryBrowserTableColumnModel();
        $renderer = new CourseCategoryBrowserTableCellRenderer($browser);
        $data_provider = new CourseCategoryBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, CourseCategoryBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_default_row_count(20);
    }
}
?>