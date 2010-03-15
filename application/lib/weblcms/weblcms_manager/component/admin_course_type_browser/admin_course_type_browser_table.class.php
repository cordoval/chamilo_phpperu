<?php
/**
 * $Id: admin_course_browser_table.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component.admin_course_browser
 */
require_once dirname(__FILE__) . '/admin_course_type_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/admin_course_type_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/admin_course_type_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../weblcms_manager.class.php';
/**
 * Table to display a set of courses.
 */
class AdminCourseTypeBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'course_type_browser_table';

    /**
     * Constructor
     */
    function AdminCourseTypeBrowserTable($browser, $parameters, $condition)
    {
        $model = new AdminCourseTypeBrowserTableColumnModel();
        $renderer = new AdminCourseTypeBrowserTableCellRenderer($browser);
        $data_provider = new AdminCourseTypeBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, AdminCourseTypeBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(WeblcmsManager :: PARAM_REMOVE_SELECTED, Translation :: get('RemoveSelected'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>