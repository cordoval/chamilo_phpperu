<?php
/**
 * $Id: subscribe_group_browser_table.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component.subscribe_group_browser
 */
require_once dirname(__FILE__) . '/subscribe_group_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/subscribe_group_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/subscribe_group_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class SubscribeGroupBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'subscribe_group_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function SubscribeGroupBrowserTable($browser, $parameters, $condition)
    {
        $model = new SubscribeGroupBrowserTableColumnModel();
        $renderer = new SubscribeGroupBrowserTableCellRenderer($browser);
        $data_provider = new SubscribeGroupBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, SubscribeGroupBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(WeblcmsManager :: PARAM_SUBSCRIBE_SELECTED_GROUP, Translation :: get('SubscribeSelected'), false);
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>