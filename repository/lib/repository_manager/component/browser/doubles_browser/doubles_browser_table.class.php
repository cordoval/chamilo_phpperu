<?php
/**
 * $Id: doubles_browser_table.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser.doubles_browser
 */
require_once dirname(__FILE__) . '/doubles_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/doubles_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/doubles_browser_table_data_provider.class.php';
/**
 * Table to display a set of learning objects.
 */
class DoublesBrowserTable extends RepositoryBrowserTable
{
    const DEFAULT_NAME = 'doubles_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function DoublesBrowserTable($browser, $parameters, $condition)
    {
        $model = new DoublesBrowserTableColumnModel();
        $renderer = new DoublesBrowserTableCellRenderer($browser);
        $data_provider = new DoublesBrowserTableDataProvider($browser, $condition);
        parent :: ObjectTable($data_provider, DoublesBrowserTable :: DEFAULT_NAME, $model, $renderer);
        
        $actions = array();
        $this->set_form_actions($actions);
        
        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
}
?>