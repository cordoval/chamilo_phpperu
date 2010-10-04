<?php
/**
 * $Id: external_link_browser_table.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.link_browser
 */
require_once dirname(__FILE__) . '/external_link_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/external_link_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/external_link_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class ExternalLinkBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'external_link_browser_table';
    
    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function ExternalLinkBrowserTable($browser, $parameters, $condition, $type)
    { 
        $this->type = $type;
        
    	$model = new ExternalLinkBrowserTableColumnModel();
        $renderer = new ExternalLinkBrowserTableCellRenderer($browser);
        $data_provider = new ExternalLinkBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME . '_' . $type, $model, $renderer);
        
        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
}
?>