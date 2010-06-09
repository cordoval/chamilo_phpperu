<?php
/**
 * $Id: pool_searcher.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once dirname(__FILE__) . '/../reservations_manager.class.php';

require_once Path :: get_plugin_path() . 'nusoap/nusoap.php';

/**
 * Component to search a pool
 */
class ReservationsManagerPoolSearcherComponent extends ReservationsManager
{
    private $client;
    private $logger;

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $pool_id = $_GET[ReservationsManager :: PARAM_CATEGORY_ID];

        if (! $this->get_user())
        {
            $this->display_header(null);
            Display :: display_error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }

        $start_array = Request :: post(Subscription :: PROPERTY_START_TIME);
        $stop_array = Request :: post(Subscription :: PROPERTY_STOP_TIME);

        $month = $start_array['F'] >= 10 ? $start_array['F'] : 0 . $start_array['F'];
        $day = $start_array['d'] >= 10 ? $start_array['d'] : 0 . $start_array['d'];
        $hour = $start_array['H'] >= 10 ? $start_array['H'] : 0 . $start_array['H'];
        $minutes = $start_array['i'] >= 10 ? $start_array['i'] : 0 . $start_array['i'];
        $start_date = $start_array['Y'] . '-' . $month . '-' . $day . ' ' . $hour . ':' . $minutes . ':00';

        $month = $stop_array['F'] >= 10 ? $stop_array['F'] : 0 . $stop_array['F'];
        $day = $stop_array['d'] >= 10 ? $stop_array['d'] : 0 . $stop_array['d'];
        $hour = $stop_array['H'] >= 10 ? $stop_array['H'] : 0 . $stop_array['H'];
        $minutes = $stop_array['i'] >= 10 ? $stop_array['i'] : 0 . $stop_array['i'];
        $stop_date = $stop_array['Y'] . '-' . $month . '-' . $day . ' ' . $hour . ':' . $minutes . ':00';

        $message = $this->search_pool($start_date, $stop_date, $pool_id);

        $bool = ($message == 'NoReservationPeriodFound');
        $this->redirect(Translation :: get($message), ($bool), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_ITEMS, ReservationsManager :: PARAM_CATEGORY_ID => $pool_id));

    }

    function search_pool($start_date, $stop_date, $pool_id)
    {
        $start_stamp = Utilities :: time_from_datepicker($start_date);
        $stop_stamp = Utilities :: time_from_datepicker($stop_date);

        //Loop through all the items
        $and_conditions = array();
        $and_conditions[] = new EqualityCondition(Item :: PROPERTY_CATEGORY, $pool_id);
        $and_conditions[] = new EqualityCondition(Item :: PROPERTY_STATUS, Item :: STATUS_NORMAL);
        $condition = new AndCondition($and_conditions);

        $items = $this->retrieve_items($condition);

        $rdm = ReservationsDataManager :: get_instance();

        while ($item = $items->next_result())
        {
            if ($item->get_blackout() == 1 || ! $this->has_right('item', $item->get_id(), ReservationsRights :: MAKE_RESERVATION_RIGHT) || ! $this->has_enough_credits_for($item, $start_stamp, $stop_stamp, $this->get_user_id()))
                continue;

            $condition = ReservationsDataManager :: get_reservations_condition($start_stamp, $stop_stamp, $item->get_id());

            //Loop through all reservations where the timespan fits
            $reservations = $this->retrieve_reservations($condition);
            $count = $reservations->size();

            $subs = array();

            while ($reservation = $reservations->next_result())
            {
                /*
				 * For blocks
				 * 1) Check if user is allready subscribed
				 * 2) Check whether the max users are reached
				 */
                if ($reservation->get_type() == Reservation :: TYPE_BLOCK)
                {
                    $and_conditions = array();
                    $res_condition = new EqualityCondition(Subscription :: PROPERTY_RESERVATION_ID, $reservation->get_id());

                    $and_conditions[] = $res_condition;
                    $and_conditions[] = new EqualityCondition(Subscription :: PROPERTY_USER_ID, $this->get_user_id());
                    $and_conditions[] = new EqualityCondition(Subscription :: PROPERTY_STATUS, Subscription :: STATUS_NORMAL);
                    $condition = new AndCondition($and_conditions);

                    $subscriptions = $this->retrieve_subscriptions($condition);
                    if ($subscriptions->size() == 0)
                    {
                        $and_conditions = array();
                        $and_conditions[] = $res_condition;
                        $and_conditions[] = new EqualityCondition(Subscription :: PROPERTY_STATUS, Subscription :: STATUS_NORMAL);
                        $condition = new AndCondition($and_conditions);

                        $subscriptions = $this->retrieve_subscriptions($condition);
                        if ($subscriptions->size() < $reservation->get_max_users())
                        {
                            if ($count == 1)
                            {
                                $subscription = $this->create_subscription($reservation, null, null, $item);
                                return 'SubscriptionCreated';
                            }
                            else
                            {
                                $subs[] = array('res' => $reservation);
                            }
                        }
                    }
                }
                /*
				 * For timepicker
				 * 1) Check whether timespan is within limits
				 * 2) Check if there are subscriptions that interfere with your timespan
				 */
                else
                {
                    $min = $reservation->get_timepicker_min();
                    $max = $reservation->get_timepicker_max();

                    $time_difference = ($stop_stamp - $start_stamp) / 60;
                    if (($min == 0 && $max == 0) || ($time_difference >= $min && $time_difference <= $max))
                    {
                        $condition = ReservationsDataManager :: get_subscriptions_condition($start_stamp, $stop_stamp, $reservation->get_id());

                        $subscriptions = $this->retrieve_subscriptions($condition);
                        //print_r($conditions);
                        if ($subscriptions->size() == 0)
                        {
                            $my_start = ($reservation->get_start_date() > $start_stamp) ? $reservation->get_start_date() : $start_stamp;
                            $my_stop = ($reservation->get_stop_date() < $stop_stamp) ? $reservation->get_stop_date() : $stop_stamp;

                            if ($count == 1)
                            {
                                $subscription = $this->create_subscription($reservation, $my_start, $my_stop, $item);
                                return 'SubscriptionCreated';
                            }
                            else
                            {
                                $subs[] = array('res' => $reservation, 'start_date' => $my_start, 'stop_date' => $my_stop);
                            }
                        }
                    }
                }
            }
            //echo count($subs) . ' ' . $count . '<br />';
            if ($count != 0 && count($subs) == $count)
            {
                foreach ($subs as $sub)
                {
                    $this->create_subscription($sub['res'], $sub['start_date'], $sub['stop_date'], $item);
                }
                return 'SubscriptionsCreated';
            }
        }

        //No reservation period found
        return 'NoReservationPeriodFound';
    }

    function create_subscription($reservation, $start_time = null, $stop_time = null, $item)
    {
        $subscription = new Subscription();
        $subscription->set_user_id($this->get_user_id());
        $subscription->set_reservation_id($reservation->get_id());
        $subscription->set_start_time($start_time);
        $subscription->set_stop_time($stop_time);
        if ($reservation->get_auto_accept() == 1)
        {
            $subscription->set_accepted(1);
        }

        if ($start_time == null)
        {
            $start_stamp = $reservation->get_start_date();
            $stop_stamp = $reservation->get_stop_date();
        }
        else
        {
            $start_stamp = $start_time;
            $stop_stamp = $stop_time;
        }

        $days = ($stop_stamp - $start_stamp) / 3600;
        $credits = $days * $item->get_credits();

        $subscription->set_weight($credits);
        $quota_box_id = ReservationsDataManager :: get_instance()->retrieve_quota_box_from_user_for_category($this->get_user_id(), $item->get_category());
        $subscription->set_quota_box($quota_box_id);

        $succes = $subscription->create();

        if ($succes)
        {
            Events :: trigger_event('create_subscription', 'reservations', array('target_id' => $subscription->get_id(), 'user_id' => $this->get_user_id()));
        }

        return $subscription;
    }

}
?>