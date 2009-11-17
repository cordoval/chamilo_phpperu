<?php
/**
 * $Id: publication_browser_table.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.publication_browser
 */
require_once dirname(__FILE__) . '/publication_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/publication_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class PublicationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'publication_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function PublicationBrowserTable($browser, $parameters, $condition)
    {
        $model = new PublicationBrowserTableColumnModel();
        $renderer = new PublicationBrowserTableCellRenderer($browser);
        $data_provider = new PublicationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, PublicationBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }

    /**
     * ContentObjectPublicationAttributes not directly extracted from the
     * database but preprocessed and are therefore not returned by the datamanager
     * as a resultset. It is instead an array which means we have to overwrite
     * this method to handle it accordingly.
     */
    function get_objects($offset, $count, $order_column)
    {
        $objects = $this->get_data_provider()->get_objects($offset, $count, $this->get_column_model()->get_order_column($order_column - ($this->has_form_actions() ? 1 : 0)));
        $table_data = array();
        $column_count = $this->get_column_model()->get_column_count();
        foreach ($objects as $object)
        {
            $row = array();
            if ($this->has_form_actions())
            {
                $row[] = $object->get_publication_object_id();
            }
            for($i = 0; $i < $column_count; $i ++)
            {
                $row[] = $this->get_cell_renderer()->render_cell($this->get_column_model()->get_column($i), $object);
            }
            $table_data[] = $row;
        }
        return $table_data;
    }
}
?>