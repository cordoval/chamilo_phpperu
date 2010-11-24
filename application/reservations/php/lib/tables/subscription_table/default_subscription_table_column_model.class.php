<?php

namespace application\reservations;

use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;
/**
 * $Id: default_subscription_table_column_model.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.tables.subscription_table
 */
/**
 * TODO: Add comment
 */
class DefaultSubscriptionTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function __construct($browser)
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

        if ($browser instanceof ReservationsManagerAdminSubscriptionBrowserComponent)
        {
            $columns[] = new ObjectTableColumn(Subscription :: PROPERTY_USER_ID, true);
        }
        elseif ($browser instanceof ReservationsManagerSubscriptionBrowserComponent)
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