<?php
/**
 * $Id: help_item_browser_table.class.php 226 2009-11-13 14:44:03Z chellee $
 * @package help.lib.help_manager.component.help_item_browser_table
 */
require_once dirname(__FILE__) . '/help_item_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/help_item_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/help_item_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class HelpItemBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'help_item_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function HelpItemBrowserTable($browser, $parameters, $condition)
    {
        $model = new HelpItemBrowserTableColumnModel();
        $renderer = new HelpItemBrowserTableCellRenderer($browser);
        $data_provider = new HelpItemBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, HelpItemBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }

    function get_objects($offset, $count, $order_column)
    {
        $help_items = $this->get_data_provider()->get_objects($offset, $count, $this->get_column_model()->get_order_column($order_column - ($this->has_form_actions() ? 1 : 0)));
        $table_data = array();
        $column_count = $this->get_column_model()->get_column_count();
        while ($help_item = $help_items->next_result())
        {
            $row = array();
            if ($this->has_form_actions())
            {
                $row[] = $help_item->get_name();
            }
            for($i = 0; $i < $column_count; $i ++)
            {
                $row[] = $this->get_cell_renderer()->render_cell($this->get_column_model()->get_column($i), $help_item);
            }
            $table_data[] = $row;
        }
        return $table_data;
    }
}
?>