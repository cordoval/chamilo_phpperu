<?php
namespace application\weblcms;

use common\libraries\ObjectTableFormAction;
use common\libraries\ObjectTable;
use common\libraries\Translation;

/**
 * $Id: admin_course_type_browser_table.class.php 218 2009-11-13 14:21:26Z Yannick $
 * @package application.lib.weblcms.weblcms_manager.component.admin_course_type_browser
 */
require_once dirname(__FILE__) . '/admin_course_type_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/admin_course_type_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/admin_course_type_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of course_types.
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

        //$actions[] = new ObjectTableFormAction('enable', Translation :: get('EnableSelectedCourseTypes'), false);
        //$actions[] = new ObjectTableFormAction('disable', Translation :: get('DisableSelectedCourseTypes'), false);
        $actions[] = new ObjectTableFormAction(WeblcmsManager :: PARAM_REMOVE_SELECTED_COURSE_TYPES, Translation :: get('RemoveSelected'));
        $actions[] = new ObjectTableFormAction(WeblcmsManager :: PARAM_ACTIVATE_SELECTED_COURSE_TYPES, Translation :: get('ActivateSelected'));
        $actions[] = new ObjectTableFormAction(WeblcmsManager :: PARAM_DEACTIVATE_SELECTED_COURSE_TYPES, Translation :: get('DeactivateSelected'));

        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>