<?php
namespace common\extensions\rights_editor_manager;
use common\libraries\ObjectTable;
/**
 * $Id: location_group_browser_table.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.rights_editor_manager.component.location_group_bowser
 */
require_once dirname(__FILE__) . '/location_group_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/location_group_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/location_group_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class LocationGroupBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'location_group_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new LocationGroupBrowserTableColumnModel($browser);
        $renderer = new LocationGroupBrowserTableCellRenderer($browser);
        $data_provider = new LocationGroupBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, LocationGroupBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
}
?>