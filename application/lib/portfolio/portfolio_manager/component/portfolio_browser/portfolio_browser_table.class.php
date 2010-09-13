<?php

require_once dirname(__FILE__) . '/portfolio_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/portfolio_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/portfolio_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../portfolio_manager.class.php';

/**
 * Table to display a set of users with portfolios.
 */
class PortfolioBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'portfolio_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function PortfolioBrowserTable($browser, $parameters, $condition)
    {
        $model = new PortfolioBrowserTableColumnModel();
        $renderer = new PortfolioBrowserTableCellRenderer($browser);
        $data_provider = new PortfolioBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, PortfolioBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>