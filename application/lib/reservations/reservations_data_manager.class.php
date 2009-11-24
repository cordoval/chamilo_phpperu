<?php

/**
 * $Id: reservations_data_manager.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations
 */
/**
 *	This is a skeleton for a data manager for the Users table.
 *	Data managers must extend this class and implement its abstract methods.
 *
 *	@author Sven Vanpoucke
 */
abstract class ReservationsDataManager
{
    private static $instance;

    protected function ReservationsDataManager()
    {
        $this->initialize();
    }

    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '.class.php';
            $class = $type . 'ReservationsDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    abstract function create_storage_unit($name, $properties, $indexes);

    abstract function delete_reservation($reservation);

    abstract function update_reservation($reservation);

    abstract function create_reservation($reservation);

    abstract function count_reservations($conditions = null);

    abstract function retrieve_reservations($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function select_next_display_order($parent_category_id);

    abstract function delete_category($category);

    abstract function update_category($category);

    abstract function create_category($category);

    abstract function count_categories($conditions = null);

    abstract function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function delete_item($item);

    abstract function update_item($item);

    abstract function create_item($item);

    abstract function count_items($conditions = null);

    abstract function retrieve_items($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function delete_quota($quota);

    abstract function update_quota($quota);

    abstract function create_quota($quota);

    abstract function count_quotas($conditions = null);

    abstract function retrieve_quotas($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function delete_subscription($subscription);

    abstract function delete_subscriptions($condition);

    abstract function create_subscription($subscription);

    abstract function update_subscription($subscription);

    abstract function count_subscriptions($conditions = null);

    abstract function retrieve_subscriptions($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function delete_quota_box($quota_box);

    abstract function update_quota_box($quota_box);

    abstract function create_quota_box($quota_box);

    abstract function count_quota_boxes($conditions = null);

    abstract function retrieve_quota_boxes($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function create_quota_rel_quota_box($quota_rel_quota_box);

    abstract function delete_quota_rel_quota_box($quota_rel_quota_box);

    abstract function delete_quota_from_quota_box($quota_box_id);

    abstract function retrieve_quota_rel_quota_boxes($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function create_quota_box_rel_category($quota_rel_quota_box);

    abstract function delete_quota_box_rel_category($quota_box_rel_category);

    abstract function empty_quota_box_rel_category($quota_box_rel_category_id);

    abstract function retrieve_quota_box_rel_categories($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function count_quota_box_rel_categories($condition = null);

    abstract function create_quota_box_rel_category_rel_user($quota_box_rel_category_rel_user);

    abstract function delete_quota_box_rel_category_rel_user($quota_box_rel_category_rel_user);

    abstract function retrieve_quota_box_rel_category_rel_users($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function create_quota_box_rel_category_rel_group($quota_box_rel_category_rel_group);

    abstract function delete_quota_box_rel_category_rel_group($quota_box_rel_category_rel_group);

    abstract function retrieve_quota_box_rel_category_rel_groups($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function create_overview_item($overview_item);

    abstract function empty_overview_for_user($user_id);

    abstract function retrieve_overview_items($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function count_overview_items($condition);

    /**
     * Check if the reservation date is free
     * Check if there are reservations who whave
     * 		- A start date between the given start, end date
     * 		- An end date between the given start, end date
     * 		- the given timewindow is part of a larger timewindow
     */
    function reservation_date_free($reservation)
    {
        $condition = $this->get_reservations_condition($reservation->get_start_date(), $reservation->get_stop_date(), $reservation->get_item(), $reservation->get_id());

        $count = $this->count_reservations($condition);

        if ($count == 0)
            return true;

        return false;

    }

    function get_reservations_condition($startdate, $enddate, $item, $id)
    {
        $or_conditions = array();

        $and_conditions = array();
        $and_conditions[] = new InEqualityCondition(Reservation :: PROPERTY_START_DATE, InEqualityCondition :: GREATER_THAN, $startdate);
        $and_conditions[] = new InEqualityCondition(Reservation :: PROPERTY_START_DATE, InEqualityCondition :: LESS_THAN, $enddate);
        $or_conditions[] = new AndCondition($and_conditions);

        $and_conditions = array();
        $and_conditions[] = new InEqualityCondition(Reservation :: PROPERTY_STOP_DATE, InEqualityCondition :: GREATER_THAN, $startdate);
        $and_conditions[] = new InEqualityCondition(Reservation :: PROPERTY_STOP_DATE, InEqualityCondition :: LESS_THAN, $enddate);
        $or_conditions[] = new AndCondition($and_conditions);

        $and_conditions = array();
        $and_conditions[] = new InEqualityCondition(Reservation :: PROPERTY_START_DATE, InEqualityCondition :: LESS_THAN_OR_EQUAL, $startdate);
        $and_conditions[] = new InEqualityCondition(Reservation :: PROPERTY_STOP_DATE, InEqualityCondition :: GREATER_THAN_OR_EQUAL, $enddate);
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
        $and_conditions[] = new InEqualityCondition(Subscription :: PROPERTY_START_TIME, InEqualityCondition :: GREATER_THAN, $starttime);
        $and_conditions[] = new InEqualityCondition(Subscription :: PROPERTY_START_TIME, InEqualityCondition :: LESS_THAN, $endtime);
        $or_conditions[] = new AndCondition($and_conditions);

        $and_conditions = array();
        $and_conditions[] = new InEqualityCondition(Subscription :: PROPERTY_STOP_TIME, InEqualityCondition :: GREATER_THAN, $starttime);
        $and_conditions[] = new InEqualityCondition(Subscription :: PROPERTY_STOP_TIME, InEqualityCondition :: LESS_THAN, $endtime);
        $or_conditions[] = new AndCondition($and_conditions);

        $and_conditions = array();
        $and_conditions[] = new InEqualityCondition(Subscription :: PROPERTY_START_TIME, InEqualityCondition :: LESS_THAN_OR_EQUAL, $starttime);
        $and_conditions[] = new InEqualityCondition(Subscription :: PROPERTY_STOP_TIME, InEqualityCondition :: GREATER_THAN_OR_EQUAL, $endtime);
        $or_conditions[] = new AndCondition($and_conditions);

        $or_condition = new OrCondition($or_conditions);

        $and_conditions = array();
        $and_conditions[] = $or_condition;
        $and_conditions[] = new EqualityCondition(Subscription :: PROPERTY_RESERVATION_ID, $reservation);
        $and_conditions[] = new EqualityCondition(Subscription :: PROPERTY_STATUS, Subscription :: STATUS_NORMAL);

        $condition = new AndCondition($and_conditions);

        return $condition;
    }

    private $used_quota = null;

    /*function calculate_used_quota($days, $user_id)
	{
		// Retrieve user quotas
		$conditions[] = new EqualityCondition(RefQuota :: PROPERTY_GROUP, 0);
		$conditions[] = new EqualityCondition(RefQuota :: PROPERTY_REF_ID, $user_id);
		$condition = new AndCondition($conditions);

		$ref_quotas = $this->retrieve_ref_quotas($condition);
		while($quota = $ref_quotas->next_result())
		{
			$quotas[] = $quota;
		}

		// Retrieve group quotas
		$gdm = GroupDataManager :: get_instance();
		$groups = $gdm->retrieve_group_rel_users(new EqualityCondition(GroupRelUser :: PROPERTY_USER_ID, $user_id));
		if($groups->size() > 0)
		{
			while($gru = $groups->next_result())
			{
				$conditions = array();
				$conditions[] = new EqualityCondition(RefQuota :: PROPERTY_GROUP, 1);
				$conditions[] = new EqualityCondition(RefQuota :: PROPERTY_REF_ID, $gru->get_group_id());
				$orconditions[] = new AndCondition($conditions);
			}

			$condition = new OrCondition($orconditions);

			$ref_quotas = $this->retrieve_ref_quotas($condition);
			while($quota = $ref_quotas->next_result())
			{
				$bool = true;

				foreach($quotas as $qu)
				{
					if($qu->get_time_unit() == $quota->get_time_unit())
					{
						$bool = false;
						break;
					}
				}

				if($bool)
					$quotas[] = $quota;
			}
		}

		// Calculate used quota
		$min_start_time = time();
		$min_start = Utilities :: to_db_date($min_start_time);

		foreach($quotas as $quota)
		{
			if($quota->get_time_unit() < $days) continue;
			$credits = 0;

			$max_start_time = strtotime('+' . $quota->get_time_unit() . ' days', $min_start_time);
			$max_start = Utilities :: to_db_date($max_start_time);

			$credits = $this->retrieve_weight_user_reservations_between($min_start, $max_start, $user_id);
			if(!$credits) $credits = 0;
			$creditlist[] = array('days' => $quota->get_time_unit(), 'max_credits' => $quota->get_credits(), 'used_credits' => $credits);
		}

		return $creditlist;
	}*/

    function calculate_used_quota($days, $category_id, $user_id)
    {
        $quota_box_id = $this->retrieve_quota_box_from_user_for_category($user_id, $category_id);
        $quota_rel_quota_boxes = $this->retrieve_quota_rel_quota_boxes(new EqualityCondition(QuotaRelQuotaBox :: PROPERTY_QUOTA_BOX_ID, $quota_box_id));
        while ($qrqb = $quota_rel_quota_boxes->next_result())
        {
            $quotas[] = $this->retrieve_quotas(new EqualityCondition(Quota :: PROPERTY_ID, $qrqb->get_quota_id()))->next_result();
        }

        // Calculate used quota
        $min_start_time = time();
        $min_start = Utilities :: to_db_date($min_start_time);

        foreach ($quotas as $quota)
        {
            if ($quota->get_time_unit() < $days)
                continue;
                //$credits = 0;


            $max_start_time = strtotime('+' . $quota->get_time_unit() . ' days', $min_start_time);
            $max_start = Utilities :: to_db_date($max_start_time);

            $credits = $this->retrieve_weight_user_reservations_between($min_start, $max_start, $user_id, $quota_box_id);
            if (! $credits)
                $credits = 0;
            $creditlist[] = array('days' => $quota->get_time_unit(), 'max_credits' => $quota->get_credits(), 'used_credits' => $credits);
        }

        return $creditlist;
    }

    function has_enough_credits_for($item, $start_date, $stop_date, $user_id)
    {
        $start_stamp = Utilities :: time_from_datepicker($start_date);
        $stop_stamp = Utilities :: time_from_datepicker($stop_date);

        $days = ($start_stamp - time()) / (3600 * 24);

        $time = $stop_stamp - $start_stamp;
        $needed_credits = $time * $item->get_credits();

        if (! $this->used_quota[$item->get_category()])
            $this->used_quota[$item->get_category()] = $this->calculate_used_quota($days, $item->get_category(), $user_id);

        //if(count($this->used_quota) == 0) return false;


        foreach ($this->used_quota[$item->get_category()] as $used_credits)
        {
            $credits = $used_credits['used_credits'] + $needed_credits;

            if ($credits > $used_credits['max_credits'])
                return false;
        }

        return true;
    }
}
?>