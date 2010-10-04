<?php
/**
 * $Id: whois_online_table.class.php 166 2009-11-12 11:03:06Z vanpouckesven $
 * @package admin.lib.admin_manager.component.whois_online_table
 */
require_once dirname(__FILE__) . '/whois_online_table_data_provider.class.php';
require_once dirname(__FILE__) . '/whois_online_table_column_model.class.php';
require_once dirname(__FILE__) . '/whois_online_table_cell_renderer.class.php';
/**
 * Table to display a set of users.
 */
class WhoisOnlineTable extends ObjectTable
{
    const DEFAULT_NAME = 'whois_online_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function WhoisOnlineTable($browser, $parameters, $condition)
    {
        $model = new WhoisOnlineTableColumnModel();
        $renderer = new WhoisOnlineTableCellRenderer($browser);
        $data_provider = new WhoisOnlineTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, WhoisOnlineTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        $this->set_form_actions($actions);
        $this->set_default_row_count(1000);
    }
}
?>