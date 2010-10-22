<?php

namespace application\reservations;

use common\libraries\WebApplication;
use common\libraries\ObjectTableFormActions;
use common\libraries\Utilities;
use common\libraries\ObjectTableFormAction;
use common\libraries\Request;
use common\libraries\ObjectTable;
/**
 * $Id: item_browser_table.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component.item_browser
 */
require_once WebApplication :: get_application_class_lib_path('reservations') . 'reservations_manager/component/item_browser/item_browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('reservations') . 'reservations_manager/component/item_browser/item_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('reservations') . 'reservations_manager/component/item_browser/item_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class ItemBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'reservations_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function ItemBrowserTable($browser, $parameters, $condition)
    {
        $model = new ItemBrowserTableColumnModel();
        $renderer = new ItemBrowserTableCellRenderer($browser);
        $data_provider = new ItemBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, Utilities :: camelcase_to_underscores(__CLASS__), $model, $renderer);
        $this->set_additional_parameters($parameters);
        
        if (get_class($browser) == 'ReservationsManagerAdminItemBrowserComponent' && $browser->get_user() && $browser->get_user()->is_platform_admin())
        {
            $actions = new ObjectTableFormActions();
            
            $actions->add_form_action(new ObjectTableFormAction(ReservationsManager :: ACTION_DELETE_ITEM, Translation :: get('RemoveSelected')));
            
            $this->set_form_actions($actions);
        }
        
        $this->set_default_row_count(20);
    }
    
	static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(ReservationsManager :: PARAM_ITEM_ID, $ids);
    }
}
?>