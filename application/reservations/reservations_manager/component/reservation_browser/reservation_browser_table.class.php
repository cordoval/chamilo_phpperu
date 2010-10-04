<?php
/**
 * $Id: reservation_browser_table.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component.reservation_browser
 */
require_once dirname(__FILE__) . '/reservation_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/reservation_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/reservation_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../reservations_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class ReservationBrowserTable extends ObjectTable
{
    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function ReservationBrowserTable($browser, $parameters, $condition)
    {
        $model = new ReservationBrowserTableColumnModel();
        $renderer = new ReservationBrowserTableCellRenderer($browser);
        $data_provider = new ReservationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, Utilities :: camelcase_to_underscores(__CLASS__), $model, $renderer);
        $this->set_additional_parameters($parameters);
        
        if (get_class($browser) == 'ReservationsManagerAdminReservationBrowserComponent' && $browser->get_user() && $browser->get_user()->is_platform_admin())
        {
            $actions = new ObjectTableFormActions();
            
            $actions->add_form_action(new ObjectTableFormAction(ReservationsManager :: ACTION_DELETE_RESERVATION, Translation :: get('RemoveSelected')));
            
            $this->set_form_actions($actions);
        }
        
        $this->set_default_row_count(20);
    }
    
	static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(ReservationsManager :: PARAM_RESERVATION_ID, $ids);
    }
}
?>