<?php

require_once dirname(__FILE__) . '/reservations_data_manager.class.php';

/**
 * $Id: reservation.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations
 */
/**
 *	@author Sven Vanpoucke
 */

class Reservation extends DataClass
{
    const PROPERTY_ITEM = 'item_id';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_START_DATE = 'start_date';
    const PROPERTY_STOP_DATE = 'stop_date';
    const PROPERTY_START_SUBSCRIPTION = 'start_subscription';
    const PROPERTY_STOP_SUBSCRIPTION = 'stop_subscription';
    const PROPERTY_MAX_USERS = 'max_users';
    const PROPERTY_NOTES = 'notes';
    const PROPERTY_TIMEPICKER_MIN = 'timepicker_min';
    const PROPERTY_TIMEPICKER_MAX = 'timepicker_max';
    const PROPERTY_AUTO_ACCEPT = 'auto_accept';
    const PROPERTY_STATUS = 'status';

    const STATUS_NORMAL = 0;
    const STATUS_DELETED = 1;

    const TYPE_TIMEPICKER = 1;
    const TYPE_BLOCK = 2;

    const CLASS_NAME = __CLASS__;

    private $subscriptions;

    /**
     * Get the default properties of all contributions.
     * @return array The property titles.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_ITEM, self :: PROPERTY_TYPE, self :: PROPERTY_START_DATE, self :: PROPERTY_STOP_DATE, self :: PROPERTY_START_SUBSCRIPTION, self :: PROPERTY_STOP_SUBSCRIPTION, self :: PROPERTY_MAX_USERS, self :: PROPERTY_NOTES, self :: PROPERTY_TIMEPICKER_MIN, self :: PROPERTY_TIMEPICKER_MAX, self :: PROPERTY_AUTO_ACCEPT, self :: PROPERTY_STATUS));
    }

    function get_data_manager()
    {
        return ReservationsDataManager :: get_instance();
    }

    function get_item()
    {
        return $this->get_default_property(self :: PROPERTY_ITEM);
    }

    function set_item($item)
    {
        $this->set_default_property(self :: PROPERTY_ITEM, $item);
    }

    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    function set_type($type)
    {
        $this->set_default_property(self :: PROPERTY_TYPE, $type);
    }

    function get_start_date()
    {
        return $this->get_default_property(self :: PROPERTY_START_DATE);
    }

    function set_start_date($start_date)
    {
        $this->set_default_property(self :: PROPERTY_START_DATE, $start_date);
    }

    function get_stop_date()
    {
        return $this->get_default_property(self :: PROPERTY_STOP_DATE);
    }

    function set_stop_date($stop_date)
    {
        $this->set_default_property(self :: PROPERTY_STOP_DATE, $stop_date);
    }

    function get_start_subscription()
    {
        return $this->get_default_property(self :: PROPERTY_START_SUBSCRIPTION);
    }

    function set_start_subscription($start_subscription)
    {
        $this->set_default_property(self :: PROPERTY_START_SUBSCRIPTION, $start_subscription);
    }

    function get_stop_subscription()
    {
        return $this->get_default_property(self :: PROPERTY_STOP_SUBSCRIPTION);
    }

    function set_stop_subscription($stop_subscription)
    {
        $this->set_default_property(self :: PROPERTY_STOP_SUBSCRIPTION, $stop_subscription);
    }

    /**
     * Returns the max_users of this contribution.
     * @return int The max_users.
     */
    function get_max_users()
    {
        return $this->get_default_property(self :: PROPERTY_MAX_USERS);
    }

    /**
     * Sets the notes of this contribution.
     * @param int $notes The notes.
     */
    function set_max_users($max_users)
    {
        $this->set_default_property(self :: PROPERTY_MAX_USERS, $max_users);
    }

    function get_notes()
    {
        return $this->get_default_property(self :: PROPERTY_NOTES);
    }

    function set_notes($notes)
    {
        $this->set_default_property(self :: PROPERTY_NOTES, $notes);
    }

    function get_timepicker_min()
    {
        return $this->get_default_property(self :: PROPERTY_TIMEPICKER_MIN);
    }

    function set_timepicker_min($timepicker_min)
    {
        $this->set_default_property(self :: PROPERTY_TIMEPICKER_MIN, $timepicker_min);
    }

    function get_timepicker_max()
    {
        return $this->get_default_property(self :: PROPERTY_TIMEPICKER_MAX);
    }

    function set_timepicker_max($timepicker_max)
    {
        $this->set_default_property(self :: PROPERTY_TIMEPICKER_MAX, $timepicker_max);
    }

    function get_auto_accept()
    {
        return $this->get_default_property(self :: PROPERTY_AUTO_ACCEPT);
    }

    function set_auto_accept($auto_accept)
    {
        $this->set_default_property(self :: PROPERTY_AUTO_ACCEPT, $auto_accept);
    }

    function get_subscriptions()
    {
        return $this->subscriptions;
    }

    function set_subscriptions($subscriptions)
    {
        $this->subscriptions = $subscriptions;
    }

    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    function create()
    {
        $rdm = ReservationsDataManager :: get_instance();
        return $rdm->create_reservation($this);
    }

    function allow_create()
    {
        $stamp_start = $this->get_start_date();
        $stamp_end = $this->get_stop_date();

        // Reservation date is not free
        if (! ReservationsDataManager :: reservation_date_free($this))
        {
            return 2;
        }

        // Subscription does not end before start of reservation
        if ($this->get_stop_subscription() != 0)
        {
            $stamp_until = $this->get_stop_subscription();
            if ($stamp_until > $stamp_start)
            {
                return 3;
            }
        }

        //Start date of reservation must be after now
        if ($this->get_start_date() < time())
        {
            return 4;
        }

        $timepicker = $this->get_type() == Reservation :: TYPE_TIMEPICKER;

        if ($timepicker)
        {
            $max = $this->get_timepicker_max();
            $min = $this->get_timepicker_min();

            if (! ($max == 0 && $min == 0))
            {
                // Maximum must be lager then minimum
                if ($max < $min)
                {
                    return 6;
                }
                else
                {
                    // The block is to large and can not fit into the given time space
                    $stamp = ($stamp_end - $stamp_start) / 60;
                    if (($stamp / $max) < 1)
                    {
                        return 7;
                    }
                }
            }
        }

        //Allow reservation to be added
        return 1;
    }

    function allow_update()
    {
        $stamp_start = $this->get_start_date();
        $stamp_end = $this->get_stop_date();

        // Reservation date is not free
        if (! ReservationsDataManager :: reservation_date_free($this))
        {
            return 2;
        }

        // Subscription does not end before start of reservation
        if ($this->get_stop_subscription() != 0)
        {
            $stamp_until = $this->get_stop_subscription();
            if ($stamp_until > $stamp_start)
            {
                return 3;
            }
        }

        $timepicker = $this->get_type() == Reservation :: TYPE_TIMEPICKER;

        // The start and end date is not the same when timepicker is chosen
        if (($stamp_start != $stamp_end) && $timepicker)
        {
            return 4;
        }

        return 1;
    }

    function update()
    {
        $rdm = ReservationsDataManager :: get_instance();

        if ($this->get_auto_accept() == 1)
        {
            $subscriptions = $rdm->retrieve_subscriptions(new EqualityCondition(Subscription :: PROPERTY_RESERVATION_ID, $this->get_id()));
            while ($subscription = $subscriptions->next_result())
            {
                $subscription->set_accepted(1);
                $subscription->update();
            }
        }

        return $rdm->update_reservation($this);
    }

    function delete()
    {
        $rdm = ReservationsDataManager :: get_instance();
        return $rdm->delete_reservation($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}