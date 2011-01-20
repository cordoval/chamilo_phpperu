<?php
namespace repository\content_object\survey_page;

use common\libraries\Translation;
use common\libraries\ObjectTable;
use common\libraries\ObjectTableFormAction;

/**
 * $Id: object_browser_table.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.assessment.component.assessment_merger
 */
require_once dirname(__FILE__) . '/object_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/object_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/object_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class ObjectBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'repository_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new ObjectBrowserTableColumnModel();
        $renderer = new ObjectBrowserTableCellRenderer($browser);
        $data_provider = new ObjectBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, ObjectBrowserTable :: DEFAULT_NAME, $model, $renderer);

        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
}
?>