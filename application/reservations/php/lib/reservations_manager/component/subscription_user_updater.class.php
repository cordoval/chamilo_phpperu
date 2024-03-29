<?php

namespace application\reservations;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Display;
use common\libraries\EqualityCondition;
use common\libraries\Utilities;
/**
 * $Id: subscription_user_updater.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.reservations_manager.component
 */
class ReservationsManagerSubscriptionUserUpdaterComponent extends ReservationsManager
{
    private $item;
    private $reservation;
    private $subscription;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $subscription_id = $_GET[ReservationsManager :: PARAM_SUBSCRIPTION_ID];
        $this->subscription = $this->retrieve_subscriptions(new EqualityCondition(Subscription :: PROPERTY_ID, $subscription_id))->next_result();
        $this->reservation = $this->retrieve_reservations(new EqualityCondition(Reservation :: PROPERTY_ID, $this->subscription->get_reservation_id()))->next_result();
        $this->item = $this->retrieve_items(new EqualityCondition(Item :: PROPERTY_ID, $this->reservation->get_item()))->next_result();
        
//        $trail = BreadcrumbTrail :: get_instance();
//
//        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
//
//        if ($this->get_user()->is_platform_admin())
//        {
//            $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_ITEMS)), Translation :: get('ManageItems')));
//            $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_RESERVATIONS, ReservationsManager :: PARAM_ITEM_ID => $this->item->get_id())), Translation :: get('ManageReservations')));
//            $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_SUBSCRIPTIONS, ReservationsManager :: PARAM_RESERVATION_ID => $this->reservation->get_id())), Translation :: get('ManageSubscriptions')));
//        }
//        else
//        {
//            $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_SUBSCRIPTIONS)), Translation :: get('MySubscriptions')));
//        }
//
//        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_SUBSCRIPTION_USERS, ReservationsManager :: PARAM_SUBSCRIPTION_ID => $this->subscription->get_id())), Translation :: get('View subscription')));
//        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_SUBSCRIPTION_ID => $subscription_id)), Translation :: get('Change subscription users')));
        
        $user = $this->get_user();
        
        if (! isset($user))
        {
            Display :: display_not_allowed($trail);
            exit();
        }
        
        $subscriptions = $this->retrieve_subscriptions(new EqualityCondition(Subscription :: PROPERTY_ID, $subscription_id));
        $subscription = $subscriptions->next_result();
        
        $form = new SubscriptionUserForm($this->get_url(array(ReservationsManager :: PARAM_SUBSCRIPTION_ID => $subscription_id)), $subscription, $user, $this->reservation, $this->item);
        
        if ($form->validate())
        {
            $success = $form->update_subscription_users();
            $object = Translation :: get('SubscriptionUser');
            $message = $success ? Translation :: get('ObjectUpdated', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES) :
                                  Translation :: get('ObjectNotUpdated', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES);
            $this->redirect($message, !$success, array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_SUBSCRIPTION_USERS, ReservationsManager :: PARAM_SUBSCRIPTION_ID => $subscription_id));
        }
        else
        {
            $this->display_header($trail);
            $form->display();
            $this->display_footer();
        }
    }
}
?>