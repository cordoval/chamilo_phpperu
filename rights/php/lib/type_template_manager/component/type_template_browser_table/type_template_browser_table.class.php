<?php
/**
 * $Id: type_template_browser_table.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.type_template_manager.component.type_template_browser_table
 */
require_once dirname(__FILE__) . '/type_template_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/type_template_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/type_template_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class TypeTemplateBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'type_template_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function TypeTemplateBrowserTable($browser, $parameters, $condition)
    {
        $model = new TypeTemplateBrowserTableColumnModel();
        $renderer = new TypeTemplateBrowserTableCellRenderer($browser);
        $data_provider = new TypeTemplateBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, TypeTemplateBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }

    function get_objects($offset, $count, $order_column)
    {
        $type_templates = $this->get_data_provider()->get_objects($offset, $count, $this->get_column_model()->get_order_column($order_column - ($this->has_form_actions() ? 1 : 0)));
        $table_data = array();
        $column_count = $this->get_column_model()->get_column_count();
        while ($type_template = $type_templates->next_result())
        {
            $row = array();
            if ($this->has_form_actions())
            {
                $row[] = $type_template->get_name();
            }
            for($i = 0; $i < $column_count; $i ++)
            {
                $row[] = $this->get_cell_renderer()->render_cell($this->get_column_model()->get_column($i), $type_template);
            }
            $table_data[] = $row;
        }
        return $table_data;
    }
}
?>