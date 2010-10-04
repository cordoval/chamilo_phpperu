<?php
/**
 * $Id: default_subscription_table_column_model.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.tables.subscription_table
 */
require_once dirname(__FILE__) . '/../../item.class.php';

/**
 * TODO: Add comment
 */
class DefaultSubscriptionTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultSubscriptionTableColumnModel($browser)
    {
        parent :: __construct(self :: get_default_columns($browser), 2);
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns($browser)
    {
        $columns = array();
        
        if (get_class($browser) == 'ReservationsManagerAdminSubscriptionBrowserComponent')
        {
            $columns[] = new ObjectTableColumn(Subscription :: PROPERTY_USER_ID, true);
        }
        elseif (get_class($browser) == 'ReservationsManagerSubscriptionBrowserComponent')
        {
            $columns[] = new ObjectTableColumn(Subscription :: PROPERTY_RESERVATION_ID, true);
        }
        $columns[] = new ObjectTableColumn(Subscription :: PROPERTY_START_TIME, true);
        $columns[] = new ObjectTableColumn(Subscription :: PROPERTY_STOP_TIME, true);
        $columns[] = new ObjectTableColumn(Subscription :: PROPERTY_ACCEPTED, true);
        return $columns;
    }
}
?>