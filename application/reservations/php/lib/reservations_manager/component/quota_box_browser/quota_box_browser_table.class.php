<?php

namespace application\reservations;

use common\libraries\WebApplication;
use common\libraries\ObjectTableFormActions;
use common\libraries\Utilities;
use common\libraries\ObjectTableFormAction;
use common\libraries\Request;
use common\libraries\ObjectTable;
use common\libraries\Translation;
/**
 * $Id: quota_box_browser_table.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component.quota_box_browser
 */
require_once WebApplication :: get_application_class_lib_path('reservations') . 'reservations_manager/component/quota_box_browser/quota_box_browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('reservations') . 'reservations_manager/component/quota_box_browser/quota_box_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('reservations') . 'reservations_manager/component/quota_box_browser/quota_box_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class QuotaBoxBrowserTable extends ObjectTable
{
    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new QuotaBoxBrowserTableColumnModel();
        $renderer = new QuotaBoxBrowserTableCellRenderer($browser);
        $data_provider = new QuotaBoxBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, Utilities :: get_classname_from_namespace(__CLASS__, true), $model, $renderer);
        $this->set_additional_parameters($parameters);

        if ($browser->get_user() && $browser->get_user()->is_platform_admin())
        {
            $actions = new ObjectTableFormActions(__NAMESPACE__);
            $actions->add_form_action(new ObjectTableFormAction(ReservationsManager :: ACTION_DELETE_QUOTA_BOX, Translation :: get('RemoveSelected', null, Utilities :: COMMON_LIBRARIES)));

            $this->set_form_actions($actions);
        }

        $this->set_default_row_count(20);
    }

	static function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);
        $ids = self :: get_selected_ids($class);
        Request :: set_get(ReservationsManager :: PARAM_QUOTA_BOX_ID, $ids);
    }
}
?>