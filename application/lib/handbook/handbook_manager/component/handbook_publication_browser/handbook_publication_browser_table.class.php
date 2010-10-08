<?php

require_once dirname(__FILE__) . '/handbook_publication_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/handbook_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/handbook_publication_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../handbook_manager.class.php';

/**
 * Table to display a set of users with handbook_publications.
 */
class HandbookPublicationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'handbook_publication_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function HandbookPublicationBrowserTable($browser, $parameters, $condition)
    {
        $model = new HandbookPublicationBrowserTableColumnModel();
        $renderer = new HandbookPublicationBrowserTableCellRenderer($browser);
        $data_provider = new HandbookPublicationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, HandbookPublicationBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>