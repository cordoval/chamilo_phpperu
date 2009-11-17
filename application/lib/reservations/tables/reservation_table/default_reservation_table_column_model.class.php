<?php
/**
 * $Id: default_reservation_table_column_model.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.tables.reservation_table
 */
require_once dirname(__FILE__) . '/../../reservation.class.php';

/**
 * TODO: Add comment
 */
class DefaultReservationTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultReservationTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(Reservation :: PROPERTY_TYPE, true);
        $columns[] = new ObjectTableColumn(Reservation :: PROPERTY_START_DATE, true);
        $columns[] = new ObjectTableColumn(Reservation :: PROPERTY_STOP_DATE, true);
        $columns[] = new ObjectTableColumn(Reservation :: PROPERTY_START_SUBSCRIPTION, true);
        $columns[] = new ObjectTableColumn(Reservation :: PROPERTY_STOP_SUBSCRIPTION, true);
        $columns[] = new ObjectTableColumn(Reservation :: PROPERTY_MAX_USERS, true);
        $columns[] = new ObjectTableColumn(Reservation :: PROPERTY_NOTES, true);
        return $columns;
    }
}
?>