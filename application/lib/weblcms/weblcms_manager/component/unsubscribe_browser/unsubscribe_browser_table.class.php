<?php
/**
 * $Id: unsubscribe_browser_table.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component.unsubscribe_browser
 */
require_once dirname(__FILE__) . '/unsubscribe_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/unsubscribe_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/unsubscribe_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../weblcms_manager.class.php';
/**
 * Table to display a list of users subscribed to a course.
 */
class UnsubscribeBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'unsubscribe_browser_table';

    /**
     * Constructor
     */
    function UnsubscribeBrowserTable($browser, $parameters, $condition)
    {
        $model = new UnsubscribeBrowserTableColumnModel();
        $renderer = new UnsubscribeBrowserTableCellRenderer($browser);
        $data_provider = new UnsubscribeBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, UnsubscribeBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_default_row_count(20);
    }
}
?>