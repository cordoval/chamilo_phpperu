<?php
/**
 * $Id: event_browser_table.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package tracking.lib.tracking_manager.component.admin_event_browser
 */
require_once dirname(__FILE__) . '/event_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/event_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/event_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class EventBrowserTable extends ObjectTable
{

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function EventBrowserTable($browser, $name, $parameters, $condition)
    {
        $model = new EventBrowserTableColumnModel();
        $renderer = new EventBrowserTableCellRenderer($browser);
        $data_provider = new EventBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, $name, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction('enable', Translation :: get('EnableSelectedEvents'), false);
        $actions[] = new ObjectTableFormAction('disable', Translation :: get('DisableSelectedEvents'), false);
        $actions[] = new ObjectTableFormAction(TrackingManager :: ACTION_EMPTY_TRACKER, Translation :: get('EmptySelectedEvents'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>