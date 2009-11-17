<?php
/**
 * $Id: quota_box_browser_table.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component.quota_box_browser
 */
require_once dirname(__FILE__) . '/quota_box_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/quota_box_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/quota_box_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../reservations_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class QuotaBoxBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'reservations_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function QuotaBoxBrowserTable($browser, $parameters, $condition)
    {
        $model = new QuotaBoxBrowserTableColumnModel();
        $renderer = new QuotaBoxBrowserTableCellRenderer($browser);
        $data_provider = new QuotaBoxBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, QuotaBoxBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        
        if ($browser->get_user() && $browser->get_user()->is_platform_admin())
        {
            $actions = array();
            $actions[] = new ObjectTableFormAction(ReservationsManager :: PARAM_REMOVE_SELECTED_QUOTA_BOXES, Translation :: get('RemoveSelected'));
            
            $this->set_form_actions($actions);
        }
        
        $this->set_default_row_count(20);
    }
}
?>