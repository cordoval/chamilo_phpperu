<?php
namespace rights;

use common\libraries\ObjectTable;

/**
 * $Id: rights_template_location_browser_table.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.rights_template_manager.component.rights_template_browser_location_table
 */
require_once dirname(__FILE__) . '/rights_template_location_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/rights_template_location_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/rights_template_location_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class RightsTemplateLocationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'location_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new RightsTemplateLocationBrowserTableColumnModel($browser);
        $renderer = new RightsTemplateLocationBrowserTableCellRenderer($browser);
        $data_provider = new RightsTemplateLocationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, RightsTemplateLocationBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }

    function get_objects($offset, $count, $order_column)
    {
        $locations = $this->get_data_provider()->get_objects($offset, $count, $this->get_column_model()->get_order_column($order_column - ($this->has_form_actions() ? 1 : 0)));
        $table_data = array();
        $column_count = $this->get_column_model()->get_column_count();
        while ($location = $locations->next_result())
        {
            $row = array();
            if ($this->has_form_actions())
            {
                $row[] = $location->get_name();
            }
            for($i = 0; $i < $column_count; $i ++)
            {
                $row[] = $this->get_cell_renderer()->render_cell($this->get_column_model()->get_column($i), $location);
            }
            $table_data[] = $row;
        }
        return $table_data;
    }
}
?>