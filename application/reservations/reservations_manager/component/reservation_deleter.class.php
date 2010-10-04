<?php
/**
 * $Id: reservation_deleter.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once dirname(__FILE__) . '/../reservations_manager.class.php';

/**
 * Component to delete an item
 */
class ReservationsManagerReservationDeleterComponent extends ReservationsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $item_id = $_GET[ReservationsManager :: PARAM_ITEM_ID];
        $ids = $_GET[ReservationsManager :: PARAM_RESERVATION_ID];

        if (! $this->get_user())
        {
            $this->display_header(null);
            Display :: display_error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }

        if ($ids)
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            $bool = true;

            foreach ($ids as $id)
            {
                //$reservations = $this->retrieve_reservations(new EqualityCondition(Reservation :: PROPERTY_ID, $id));
                //$reservation = $reservations->next_result();


                $reservation = new Reservation();
                $reservation->set_id($id);

                //$reservation->set_status(Reservation :: STATUS_DELETED);


                if (! $reservation->delete())
                {
                    $bool = false;
                }
                else
                {
                    Event :: trigger('delete_reservation', ReservationsManager :: APPLICATION_NAME, array(ChangesTracker :: PROPERTY_REFERENCE_ID => $id, ChangesTracker :: PROPERTY_USER_ID => $this->get_user_id()));
                }
            }

            if (count($ids) == 1)
                $message = $bool ? 'ReservationsDeleted' : 'ReservationsNotDeleted';
            else
                $message = $bool ? 'ReservationsDeleted' : 'ReservationsNotDeleted';

            $this->redirect(Translation :: get($message), ($bool ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_RESERVATIONS, ReservationsManager :: PARAM_ITEM_ID => $item_id));
        }
        else
        {
            $this->display_header();
            $this->display_error_message(Translation :: get("NoObjectSelected"));
            $this->display_footer();
        }
    }

}
?>