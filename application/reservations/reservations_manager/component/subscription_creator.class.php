<?php
/**
 * $Id: subscription_creator.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once dirname(__FILE__) . '/../reservations_manager.class.php';

require_once dirname(__FILE__) . '/../../subscription.class.php';
require_once dirname(__FILE__) . '/../../forms/subscription_form.class.php';
require_once dirname(__FILE__) . '/../../reservations_data_manager.class.php';

class ReservationsManagerSubscriptionCreatorComponent extends ReservationsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $reservation_id = $_GET[ReservationsManager :: PARAM_RESERVATION_ID];
        $reservation = $this->retrieve_reservations(new EqualityCondition(Reservation :: PROPERTY_ID, $reservation_id))->next_result();
        $item = $this->retrieve_items(new EqualityCondition(Item :: PROPERTY_ID, $reservation->get_item()))->next_result();
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_ITEMS)), Translation :: get('ViewItems')));
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_RESERVATIONS, ReservationsManager :: PARAM_ITEM_ID => $reservation->get_item())), Translation :: get('ViewReservations')));
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_RESERVATION_ID => $reservation_id)), Translation :: get('CreateSubscription')));
        
        $user = $this->get_user();
        
        if (! isset($user))
        {
            Display :: display_not_allowed($trail);
            exit();
        }
        
        $subscription = new Subscription();
        $subscription->set_reservation_id(isset($reservation_id) ? $reservation_id : 0);
        $subscription->set_user_id($this->get_user_id());
        
        $form = new SubscriptionForm($this->get_url(array(ReservationsManager :: PARAM_RESERVATION_ID => $reservation_id)), $subscription, $reservation, $item, $user);
        $status = $form->allow_create_subscription();
        
        if ($status == 1)
        {
            $success = $form->create_subscription();
            $this->redirect(Translation :: get($success ? 'SubscriptionCreated' : 'SubscriptionNotCreated'), ($success ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_RESERVATIONS, ReservationsManager :: PARAM_ITEM_ID => $reservation->get_item()));
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
            case 2 :
                return Translation :: get('UserAllreadySubscribed');
            case 3 :
                return Translation :: get('MaxUsersReached');
            case 4 :
                return Translation :: get('ChosenTimeNotInReservationPeriod');
            case 5 :
                return Translation :: get('TimewindowNotInTimepickerLimits');
            case 6 :
                return Translation :: get('AnotherSubscription');
            case 7 :
                return Translation :: get('OutOfSubscriptionPeriod');
            case 8 :
                return Translation :: get('NotEnoughCredits');
            case 9 :
                return Translation :: get('StartTimeInPast');
        }
    }
}
?>