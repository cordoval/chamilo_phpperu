<?php
/**
 * $Id: subscription_overview_browser_table_column_model.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component.subscription_overview_browser
 */
require_once dirname(__FILE__) . '/../../../tables/subscription_table/default_subscription_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../subscription.class.php';
/**
 * Table column model for the user browser table
 */
class SubscriptionOverviewBrowserTableColumnModel extends DefaultSubscriptionTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function SubscriptionOverviewBrowserTableColumnModel($browser)
    {
        parent :: ObjectTableColumnModel(self :: get_default_columns($browser), 2);
    }

    private static function get_default_columns($browser)
    {
        $columns = array();
        //$columns[] = new StaticTableColumn(Translation :: get(Utilities :: underscores_to_camelcase(Subscription :: PROPERTY_RESERVATION_ID)));
        //$columns[] = new StaticTableColumn(Translation :: get(Utilities :: underscores_to_camelcase(Subscription :: PROPERTY_USER_ID)));
        

        $table_name = ReservationsDataManager :: get_instance()->get_alias(Item :: get_table_name());
        $u_table_name = ReservationsDataManager :: get_instance()->get_alias(User :: get_table_name());
        
        $columns[] = new ObjectTableColumn(Item :: PROPERTY_NAME, true, $table_name);
        $columns[] = new ObjectTableColumn(User :: PROPERTY_FIRSTNAME, true, $u_table_name);
        $columns[] = new ObjectTableColumn(Subscription :: PROPERTY_START_TIME, false);
        $columns[] = new ObjectTableColumn(Subscription :: PROPERTY_STOP_TIME, false);
        $columns[] = new ObjectTableColumn(Subscription :: PROPERTY_ACCEPTED, true);
        $columns[] = new ObjectTableColumn('AdditionalUsers', false);
        return $columns;
    }

}
?>
