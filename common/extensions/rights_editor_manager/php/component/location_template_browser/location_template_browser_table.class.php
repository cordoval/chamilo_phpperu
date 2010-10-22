<?php
namespace common\extensions\rights_editor_manager;
use common\libraries\ObjectTable;
/**
 * $Id: location_template_browser_table.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 */
require_once dirname(__FILE__) . '/location_template_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/location_template_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/location_template_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class LocationTemplateBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'rights_template_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function LocationTemplateBrowserTable($browser, $parameters, $condition)
    {
        $model = new LocationTemplateBrowserTableColumnModel($browser);
        $renderer = new LocationTemplateBrowserTableCellRenderer($browser);
        $data_provider = new LocationTemplateBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, LocationTemplateBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
}
?>