<?php
/**
 * $Id: default_subscription_user_table_column_model.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.tables.subscription_user_table
 */

/**
 * TODO: Add comment
 */
class DefaultSubscriptionUserTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultSubscriptionUserTableColumnModel($browser)
    {
        parent :: __construct(self :: get_default_columns($browser), 1);
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns($browser)
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(SubscriptionUser :: PROPERTY_USER_ID, true);
        return $columns;
    }
}
?>