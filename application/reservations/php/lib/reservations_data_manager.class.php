<?php

namespace application\reservations;

use common\libraries\Configuration;
use common\libraries\WebApplication;
use common\libraries\AndCondition;
use common\libraries\OrCondition;
use common\libraries\NotCondition;
use common\libraries\EqualityCondition;
use common\libraries\InequalityCondition;

/**
 * $Id: reservations_data_manager.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations
 */
/**
 * This is a skeleton for a data manager for the Users table.
 * Data managers must extend this class and implement its abstract methods.
 *
 * @author Sven Vanpoucke
 */
class ReservationsDataManager
{
    private static $instance;

    public function __construct()
    {
        $this->initialize();
    }

    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once WebApplication :: get_application_class_lib_path('reservations') . 'data_manager/' . strtolower($type) . '_reservations_data_manager.class.php';
            $class = __NAMESPACE__ . '\\' . $type . 'ReservationsDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    /**
     * Check if the reservation date is free
     * Check if there are reservations who whave
     * - A start date between the given start, end date
     * - An end date between the given start, end date
     * - the given timewindow is part of a larger timewindow
     */
    function reservation_date_free($reservation)
    {
        $condition = self :: get_reservations_condition($reservation->get_start_date(), $reservation->get_stop_date(), $reservation->get_item(), $reservation->get_id());

        $count = self :: get_instance()->count_reservations($condition);

        if ($count == 0)
        {
            return true;
        }

        return false;

    }

    function get_reservations_condition($startdate, $enddate, $item, $id)
    {
        $or_conditions = array();

        $and_conditions = array();
        $and_conditions[] = new InequalityCondition(Reservation :: PROPERTY_START_DATE, InequalityCondition :: GREATER_THAN, $startdate);
        $and_conditions[] = new InequalityCondition(Reservation :: PROPERTY_START_DATE, InequalityCondition :: LESS_THAN, $enddate);
        $or_conditions[] = new AndCondition($and_conditions);

        $and_conditions = array();
        $and_conditions[] = new InequalityCondition(Reservation :: PROPERTY_STOP_DATE, InequalityCondition :: GREATER_THAN, $startdate);
        $and_conditions[] = new InequalityCondition(Reservation :: PROPERTY_STOP_DATE, InequalityCondition :: LESS_THAN, $enddate);
        $or_conditions[] = new AndCondition($and_conditions);

        $and_conditions = array();
        $and_conditions[] = new InequalityCondition(Reservation :: PROPERTY_START_DATE, InequalityCondition :: LESS_THAN_OR_EQUAL, $startdate);
        $and_conditions[] = new InequalityCondition(Reservation :: PROPERTY_STOP_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, $enddate);
        $or_conditions[] = new AndCondition($and_conditions);

        $or_condition = new OrCondition($or_conditions);

        $and_conditions = array();
        $and_conditions[] = $or_condition;
        $and_conditions[] = new EqualityCondition(Reservation :: PROPERTY_ITEM, $item);
        $and_conditions[] = new EqualityCondition(Reservation :: PROPERTY_STATUS, Reservation :: STATUS_NORMAL);

        if ($id)
        {
            $and_conditions[] = new NotCondition(new EqualityCondition(Reservation :: PROPERTY_ID, $id));
        }

        $condition = new AndCondition($and_conditions);

        return $condition;
    }

    function get_subscriptions_condition($starttime, $endtime, $reservation)
    {
        $or_conditions = array();

        $and_conditions = array();
        $and_conditions[] = new InequalityCondition(Subscription :: PROPERTY_START_TIME, InequalityCondition :: GREATER_THAN, $starttime);
        $and_conditions[] = new InequalityCondition(Subscription :: PROPERTY_START_TIME, InequalityCondition :: LESS_THAN, $endtime);
        $or_conditions[] = new AndCondition($and_conditions);

        $and_conditions = array();
        $and_conditions[] = new InequalityCondition(Subscription :: PROPERTY_STOP_TIME, InequalityCondition :: GREATER_THAN, $starttime);
        $and_conditions[] = new InequalityCondition(Subscription :: PROPERTY_STOP_TIME, InequalityCondition :: LESS_THAN, $endtime);
        $or_conditions[] = new AndCondition($and_conditions);

        $and_conditions = array();
        $and_conditions[] = new InequalityCondition(Subscription :: PROPERTY_START_TIME, InequalityCondition :: LESS_THAN_OR_EQUAL, $starttime);
        $and_conditions[] = new InequalityCondition(Subscription :: PROPERTY_STOP_TIME, InequalityCondition :: GREATER_THAN_OR_EQUAL, $endtime);
        $or_conditions[] = new AndCondition($and_conditions);

        $or_condition = new OrCondition($or_conditions);

        $and_conditions = array();
        $and_conditions[] = $or_condition;
        $and_conditions[] = new EqualityCondition(Subscription :: PROPERTY_RESERVATION_ID, $reservation);
        $and_conditions[] = new EqualityCondition(Subscription :: PROPERTY_STATUS, Subscription :: STATUS_NORMAL);

        $condition = new AndCondition($and_conditions);

        return $condition;
    }

    private static $used_quota = null;

    function calculate_used_quota($days, $category_id, $user_id)
    {
        $quota_box_id = self :: get_instance()->retrieve_quota_box_from_user_for_category($user_id, $category_id);
        $quota_rel_quota_boxes = self :: get_instance()->retrieve_quota_rel_quota_boxes(new EqualityCondition(QuotaRelQuotaBox :: PROPERTY_QUOTA_BOX_ID, $quota_box_id));
        while ($qrqb = $quota_rel_quota_boxes->next_result())
        {
            $quotas[] = self :: get_instance()->retrieve_quotas(new EqualityCondition(Quota :: PROPERTY_ID, $qrqb->get_quota_id()))->next_result();
        }

        // Calculate used quota
        $min_start = time();

        foreach ($quotas as $quota)
        {
            if ($quota->get_time_unit() < $days)
            {
                continue;
            }

            $max_start = strtotime('+' . $quota->get_time_unit() . ' days', $min_start);

            $credits = self :: get_instance()->retrieve_weight_user_reservations_between($min_start, $max_start, $user_id, $quota_box_id);
            if (! $credits)
            {
                $credits = 0;
            }
            $creditlist[] = array('days' => $quota->get_time_unit(), 'max_credits' => $quota->get_credits(), 'used_credits' => $credits);
        }

        return $creditlist;
    }

    function has_enough_credits_for($item, $start_date, $stop_date, $user_id)
    {
        $days = ($start_date - time()) / (3600 * 24);

        $time = ($stop_date - $start_date) / 3600;
        $needed_credits = $time * $item->get_credits();

        if (! self :: $used_quota[$item->get_category()])
        {
            self :: $used_quota[$item->get_category()] = self :: calculate_used_quota($days, $item->get_category(), $user_id);
        }

        foreach (self :: $used_quota[$item->get_category()] as $used_credits)
        {
            $credits = $used_credits['used_credits'] + $needed_credits;

            if ($credits > $used_credits['max_credits'])
            {
                return false;
            }
        }

        return true;
    }
}
?>