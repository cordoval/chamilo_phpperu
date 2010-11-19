<?php

namespace application\reservations;

use common\libraries\EqualityCondition;
use tracking\Event;
use common\libraries\Translation;
use common\libraries\Utilities;
/**
 * $Id: subscription_deleter.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.reservations_manager.component
 */

/**
 * Component to delete a subscription
 */
class ReservationsManagerSubscriptionDeleterComponent extends ReservationsManager
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
            Display :: display_error_message(Translation :: get('NotAllowed', null, Utilities :: COMMON_LIBRARIES));
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
                    Event :: trigger('delete_subscription', ReservationsManager :: APPLICATION_NAME, array(ChangesTracker :: PROPERTY_REFERENCE_ID => $id, ChangesTracker :: PROPERTY_USER_ID => $this->get_user_id()));
                }

                $subscriptionuser = new SubscriptionUser();
                $subscriptionuser->set_subscription_id($subscription->get_id());
                $subscriptionuser->delete();
            }

            if (count($ids) == 1)
            {
                $object = Translation :: get('Subscription');
                $message = $bool ? Translation :: get('ObjectDeleted', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES) :
                                   Translation :: get('ObjectNotDeleted', array('OBJECT' => $object), Utilities :: COMMON_LIBRARIES);
            }
            else
            {
                $objects = Translation :: get('Subscriptions');
                $message = $bool ? Translation :: get('ObjectsDeleted', array('OBJECTS' => $objects), Utilities :: COMMON_LIBRARIES) :
                                   Translation :: get('ObjectsNotDeleted', array('OBJECTS' => $objects), Utilities :: COMMON_LIBRARIES);
            }

            $this->redirect($message, !$bool, array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_SUBSCRIPTIONS));
        }
        else
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NoObjectSelected', null, Utilities :: COMMON_LIBRARIES));
            $this->display_footer();
        }
    }

}
?>