<?php
/**
 * $Id: subscription_deleter.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once dirname(__FILE__) . '/../reservations_manager.class.php';
require_once dirname(__FILE__) . '/../reservations_manager_component.class.php';

/**
 * Component to delete a subscription
 */
class ReservationsManagerSubscriptionDeleterComponent extends ReservationsManagerComponent
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
            
            foreach ($ids as $id)
            {
                $subscriptions = $this->retrieve_subscriptions(new EqualityCondition(Subscription :: PROPERTY_ID, $id));
                $subscription = $subscriptions->next_result();
                
                /*$subscription->set_status(Subscription :: STATUS_DELETED);*/
                if (! $subscription->delete())
                {
                    $bool = false;
                }
                else
                {
                    Events :: trigger_event('delete_subscription', 'reservations', array('target_id' => $id, 'user_id' => $this->get_user_id()));
                }
                
                $subscriptionuser = new SubscriptionUser();
                $subscriptionuser->set_subscription_id($subscription->get_id());
                $subscriptionuser->delete();
            }
            
            if (count($ids) == 1)
                $message = $bool ? 'SubscriptionDeleted' : 'SubscriptionNotDeleted';
            else
                $message = $bool ? 'SubscriptionsDeleted' : 'SubscriptionsNotDeleted';
            
            $this->redirect(Translation :: get($message), ($bool ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_SUBSCRIPTIONS));
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