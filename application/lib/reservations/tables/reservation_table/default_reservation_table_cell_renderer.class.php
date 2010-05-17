<?php
/**
 * $Id: default_reservation_table_cell_renderer.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.tables.reservation_table
 */

require_once dirname(__FILE__) . '/../../reservation.class.php';
/**
 * TODO: Add comment
 */
class DefaultReservationTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultReservationTableCellRenderer($browser)
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $reservation)
    {
        if ($property = $column->get_name())
        {
            switch ($property)
            {
                case Reservation :: PROPERTY_TYPE :
                    switch ($reservation->get_type())
                    {
                        case Reservation :: TYPE_TIMEPICKER :
                            return Translation :: get('Timepicker');
                        case Reservation :: TYPE_BLOCK :
                            return Translation :: get('Block');
                    }
                case Reservation :: PROPERTY_START_DATE :
                    return DatetimeUtilities :: format_locale_date(null, $reservation->get_start_date());
                case Reservation :: PROPERTY_STOP_DATE :
                    return DatetimeUtilities :: format_locale_date(null, $reservation->get_stop_date());
                case Reservation :: PROPERTY_NOTES :
                    $notes = strip_tags($reservation->get_notes());
                    if (strlen($notes) > 175)
                    {
                        $notes = mb_substr($notes, 0, 170) . '&hellip;';
                    }
                    return '<div style="word-wrap: break-word; max-width: 250px;" >' . $notes . '</div>';
                case Reservation :: PROPERTY_START_SUBSCRIPTION :
                	if($reservation->get_start_subscription() > 0)
                	{
                    	return DatetimeUtilities :: format_locale_date(null, $reservation->get_start_subscription());
                	}
                	return;
                case Reservation :: PROPERTY_STOP_SUBSCRIPTION :
                	if($reservation->get_stop_subscription() > 0)
                	{
                    	return DatetimeUtilities :: format_locale_date(null, $reservation->get_stop_subscription());
                	}
                	return;
                case Reservation :: PROPERTY_MAX_USERS :
                    return $reservation->get_max_users();
            }
        
        }
    }

    function render_id_cell($reservation)
    {
        return $reservation->get_id();
    }
}
?>