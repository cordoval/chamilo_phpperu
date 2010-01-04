<?php
/**
 * $Id: link_browser_table.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.link_browser
 */
require_once dirname(__FILE__) . '/link_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/link_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/link_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class LinkBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'link_browser_table';

    const PARAM_TYPE = 'type';
    
    const TYPE_PUBLICATIONS = 1;
    const TYPE_PARENTS = 2;
    const TYPE_CHILDREN = 3;
    const TYPE_ATTACHMENTS = 4;
    const TYPE_INCLUDES = 5;
    
    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function LinkBrowserTable($browser, $parameters, $condition, $type)
    {
        $model = new LinkBrowserTableColumnModel($type);
        $renderer = new LinkBrowserTableCellRenderer($browser, $type);
        $data_provider = new LinkBrowserTableDataProvider($browser, $condition, $type);
        parent :: __construct($data_provider, LinkBrowserTable :: DEFAULT_NAME, $model, $renderer);
        
        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
}
?>