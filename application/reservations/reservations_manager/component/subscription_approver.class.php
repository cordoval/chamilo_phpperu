<?php
/**
 * $Id: subscription_approver.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once dirname(__FILE__) . '/../reservations_manager.class.php';


/**
 * Component to delete a subscription
 */
class ReservationsManagerSubscriptionApproverComponent extends ReservationsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = $_GET[ReservationsManager :: PARAM_SUBSCRIPTION_ID];

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
            $reservation_id = 0;

            foreach ($ids as $id)
            {
                $subscriptions = $this->retrieve_subscriptions(new EqualityCondition(Subscription :: PROPERTY_ID, $id));
                $subscription = $subscriptions->next_result();

                if ($reservation_id == 0)
                    $reservation_id = $subscription->get_reservation_id();

                $subscription->set_accepted(1);
                if (! $subscription->update())
                {
                    $bool = false;
                }
                else
                {
                    Event :: trigger('approve_subscription', ReservationsManager :: APPLICATION_NAME, array(ChangesTracker :: PROPERTY_REFERENCE_ID => $id, ChangesTracker :: PROPERTY_USER_ID => $this->get_user_id()));
                }
            }

            if (count($ids) == 1)
                $message = $bool ? 'SubscriptionsApproved' : 'SubscriptionsNotApproved';
            else
                $message = $bool ? 'SubscriptionsApproved' : 'SubscriptionsNotApproved';

            $this->redirect(Translation :: get($message), ($bool ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_SUBSCRIPTIONS, ReservationsManager :: PARAM_RESERVATION_ID => $reservation_id));
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