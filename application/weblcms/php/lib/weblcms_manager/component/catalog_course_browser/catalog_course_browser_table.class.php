<?php
namespace application\weblcms;

use common\libraries\ObjectTableFormAction;
use common\libraries\ObjectTable;
use common\libraries\Translation;
use common\libraries\Utilities;

require_once dirname(__FILE__) . '/catalog_course_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/catalog_course_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/catalog_course_browser_table_cell_renderer.class.php';

/**
 * Table to display a list of courses. Display home and register actions if available.
 */
class CatalogCourseBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'catalog_course_browser_table';

    /**
     * Constructor
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new CatalogCourseBrowserTableColumnModel();
        $renderer = new CatalogCourseBrowserTableCellRenderer($browser);
        $data_provider = new CatalogCourseBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, CatalogCourseBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $actions = array();

        $actions[] = new ObjectTableFormAction(WeblcmsManager :: PARAM_REMOVE_SELECTED, Translation :: get('RemoveSelected', null ,Utilities:: COMMON_LIBRARIES));
        $actions[] = new ObjectTableFormAction(WeblcmsManager :: PARAM_CHANGE_COURSE_TYPE_SELECTED_COURSES, Translation :: get('ChangeCourseTypeSelected'), false);

        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>