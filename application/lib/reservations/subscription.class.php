<?php

/**
 * $Id: subscription.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations
 */
require_once dirname(__FILE__) . '/reservations_data_manager.class.php';

/**
 * @author Sven Vanpoucke
 */

class Subscription extends DataClass
{
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_RESERVATION_ID = 'reservation_id';
    const PROPERTY_START_TIME = 'start_time';
    const PROPERTY_STOP_TIME = 'stop_time';
    const PROPERTY_ACCEPTED = 'accepted';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_WEIGHT = 'weight';
    const PROPERTY_QUOTA_BOX = 'quota_box';

    const STATUS_NORMAL = 0;
    const STATUS_DELETED = 1;

    const CLASS_NAME = __CLASS__;

    /**
     * Get the default properties of all contributions.
     * @return array The property titles.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_USER_ID, self :: PROPERTY_RESERVATION_ID, self :: PROPERTY_START_TIME, self :: PROPERTY_STOP_TIME, self :: PROPERTY_ACCEPTED, self :: PROPERTY_STATUS, self :: PROPERTY_WEIGHT, self :: PROPERTY_QUOTA_BOX));
    }

    function get_data_manager()
    {
        return ReservationsDataManager :: get_instance();
    }

    /**
     * Returns the user_id of this contribution.
     * @return int The user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Sets the reservation_id of this contribution.
     * @param int $reservation_id The reservation_id.
     */
    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_reservation_id()
    {
        return $this->get_default_property(self :: PROPERTY_RESERVATION_ID);
    }

    function set_reservation_id($reservation_id)
    {
        $this->set_default_property(self :: PROPERTY_RESERVATION_ID, $reservation_id);
    }

    function get_start_time()
    {
        return $this->get_default_property(self :: PROPERTY_START_TIME);
    }

    function set_start_time($start_time)
    {
        $this->set_default_property(self :: PROPERTY_START_TIME, $start_time);
    }

    function get_stop_time()
    {
        return $this->get_default_property(self :: PROPERTY_STOP_TIME);
    }

    function set_stop_time($stop_time)
    {
        $this->set_default_property(self :: PROPERTY_STOP_TIME, $stop_time);
    }

    function get_accepted()
    {
        return $this->get_default_property(self :: PROPERTY_ACCEPTED);
    }

    function set_accepted($accepted)
    {
        $this->set_default_property(self :: PROPERTY_ACCEPTED, $accepted);
    }

    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    function get_weight()
    {
        return $this->get_default_property(self :: PROPERTY_WEIGHT);
    }

    function set_weight($weight)
    {
        $this->set_default_property(self :: PROPERTY_WEIGHT, $weight);
    }

    function get_quota_box()
    {
        return $this->get_default_property(self :: PROPERTY_QUOTA_BOX);
    }

    function set_quota_box($quota_box)
    {
        $this->set_default_property(self :: PROPERTY_QUOTA_BOX, $quota_box);
    }

    function allow_create($user)
    {
        $rdm = ReservationsDataManager :: get_instance();
        $reservation = $rdm->retrieve_reservations(new EqualityCondition(Reservation :: PROPERTY_ID, $this->get_reservation_id()))->next_result();
        $item = $rdm->retrieve_items(new EqualityCondition(Item :: PROPERTY_ID, $reservation->get_item()))->next_result();

        $conditions[] = new EqualityCondition(Subscription :: PROPERTY_RESERVATION_ID, $this->get_reservation_id());
        $conditions[] = new EqualityCondition(Subscription :: PROPERTY_STATUS, Subscription :: STATUS_NORMAL);
        $reservation_condition = new AndCondition($conditions);

        if (! $this->get_start_time())
        {
            $start = $reservation->get_start_date();
            $stop = $reservation->get_stop_date();
        }
        else
        {
            $start = $this->get_start_time();
            $stop = $this->get_stop_time();
        }

        if (! ReservationsDataManager :: has_enough_credits_for($item, $start, $stop, $user->get_id()))
            return 8;

        if ($reservation->get_type() == Reservation :: TYPE_BLOCK)
        {
            $user_condition = new EqualityCondition(Subscription :: PROPERTY_USER_ID, $user->get_id());
            $cond = new AndCondition(array($user_condition, $reservation_condition));

            //User is allready subscribed
            $count = $rdm->count_subscriptions($cond);
            if ($count != 0)
                return 2;

            //Max users is reached
            $count = $rdm->count_subscriptions($reservation_condition);
            if ($count > $reservation->get_max_users())
                return 3;
        }
        else
        {
            $stamp_sub_start = $this->get_start_time();
            $stamp_sub_end = $this->get_stop_time();

            $stamp_res_start = $reservation->get_start_date();
            $stamp_res_end = $reservation->get_stop_date();

            //Chosen time is out of reservation period
            if (($stamp_sub_start < $stamp_res_start) || ($stamp_sub_end > $stamp_res_end))
                return 4;

            //Chosen time is smaller than now
            if ($stamp_sub_start < time())
                return 9;

            //Timewindow is not between timepicker min and max values
            $difference = $stamp_sub_end - $stamp_sub_start;
            if ($reservation->get_timepicker_min() > 0)
                if (($difference < $reservation->get_timepicker_min() * 60) || ($difference > $reservation->get_timepicker_max() * 60))
                    return 5;

            //There is allready a subscription that overlaps this period
            $condition = ReservationsDataManager :: get_subscriptions_condition($this->get_start_time(), $this->get_stop_time(), $this->get_reservation_id());
            $count = $rdm->count_subscriptions($condition);
            if ($count != 0)
                return 6;

        }

        //You can not subscribe at this moment because you are not in the subscription period
        $now = time();
        if ($reservation->get_start_subscription())
        {
            if ($now < $reservation->get_start_subscription() || $now > $reservation->get_stop_subscription())
                return 7;
        }

        //Allow subscription to be added
        return 1;
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}