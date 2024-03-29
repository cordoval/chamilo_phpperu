<?php

namespace application\reservations;

use common\libraries\WebApplication;
use common\libraries\ObjectTable;
/**
 * $Id: subscription_overview_browser_table.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component.subscription_overview_browser
 */
require_once WebApplication :: get_application_class_lib_path('reservations') . 'reservations_manager/component/subscription_overview_browser/subscription_overview_browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('reservations') . 'reservations_manager/component/subscription_overview_browser/subscription_overview_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('reservations') . 'reservations_manager/component/subscription_overview_browser/subscription_overview_browser_table_cell_renderer.class.php';

/**
 * Table to display a set of learning objects.
 */
class SubscriptionOverviewBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'reservations_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new SubscriptionOverviewBrowserTableColumnModel($browser);
        $renderer = new SubscriptionOverviewBrowserTableCellRenderer($browser);
        $data_provider = new SubscriptionOverviewBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, SubscriptionOverviewBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
}
?>