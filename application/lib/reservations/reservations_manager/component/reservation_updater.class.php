<?php
/**
 * $Id: reservation_updater.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once dirname(__FILE__) . '/../reservations_manager.class.php';
require_once dirname(__FILE__) . '/../reservations_manager_component.class.php';
require_once dirname(__FILE__) . '/../../reservation.class.php';
require_once dirname(__FILE__) . '/../../forms/reservation_form.class.php';
require_once dirname(__FILE__) . '/../../reservations_data_manager.class.php';

class ReservationsManagerReservationUpdaterComponent extends ReservationsManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $item_id = $_GET[ReservationsManager :: PARAM_ITEM_ID];
        $reservation_id = $_GET[ReservationsManager :: PARAM_RESERVATION_ID];
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_ITEMS)), Translation :: get('ManageItems')));
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_RESERVATIONS, ReservationsManager :: PARAM_ITEM_ID => $item_id)), Translation :: get('ManageReservations')));
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_RESERVATION_ID => $reservation_id, ReservationsManager :: PARAM_ITEM_ID => $item_id)), Translation :: get('UpdateReservation')));
        
        $user = $this->get_user();
        
        if (! isset($user))
        {
            Display :: display_not_allowed($trail);
            exit();
        }
        
        $reservations = $this->retrieve_reservations(new EqualityCondition(Reservation :: PROPERTY_ID, $reservation_id));
        $reservation = $reservations->next_result();
        
        $form = new ReservationForm(ReservationForm :: TYPE_EDIT, $this->get_url(array(ReservationsManager :: PARAM_RESERVATION_ID => $reservation->get_id(), ReservationsManager :: PARAM_ITEM_ID => $item_id)), $reservation, $user);
        $status = $form->allow_update_reservation();
        
        if ($status == 1)
        {
            $success = $form->update_reservation();
            $this->redirect(Translation :: get($success ? 'ReservationUpdated' : 'ReservationNotUpdated'), ($success ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_RESERVATIONS, ReservationsManager :: PARAM_ITEM_ID => $item_id));
        }
        else
        {
            $_GET['message'] = $this->parse_status($status);
            $this->display_header($trail);
            $form->display();
            $this->display_footer();
        }
    }

    function parse_status($status)
    {
        switch ($status)
        {
            case 0 :
                return null;
            case 2 :
                return Translation :: get('ReservationDateNotFree');
            case 3 :
                return Translation :: get('SubscriptionEndAfterStart');
            case 4 :
                return Translation :: get('NoEqualDatesWithTimepicker');
        }
    }
}
?>