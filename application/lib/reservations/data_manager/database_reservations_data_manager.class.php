<?php
/**
 * $Id: database_reservations_data_manager.class.php 230 2009-11-16 09:29:45Z vanpouckesven $
 * @package application.reservations.data_manager
 */
require_once dirname(__FILE__) . '/../reservations_data_manager.class.php';
require_once dirname(__FILE__) . '/../category.class.php';
require_once dirname(__FILE__) . '/../item.class.php';
require_once dirname(__FILE__) . '/../quota.class.php';
require_once dirname(__FILE__) . '/../quota_box.class.php';
require_once dirname(__FILE__) . '/../quota_box_rel_category.class.php';
require_once dirname(__FILE__) . '/../quota_box_rel_category_rel_user.class.php';
require_once dirname(__FILE__) . '/../quota_box_rel_category_rel_group.class.php';
require_once dirname(__FILE__) . '/../quota_rel_quota_box.class.php';
require_once dirname(__FILE__) . '/../reservation.class.php';
require_once dirname(__FILE__) . '/../subscription.class.php';
require_once dirname(__FILE__) . '/../subscription_user.class.php';
require_once dirname(__FILE__) . '/../overview_item.class.php';
require_once dirname(__FILE__) . '/../reservations_data_manager_interface.class.php';

/**
==============================================================================
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *	@author Tim De Pauw
 *	@author Bart Mollet
==============================================================================
 */

class DatabaseReservationsDataManager extends Database implements ReservationsDataManagerInterface
{
    /**
     * Initializes the connection
     */
    function initialize()
    {
        parent :: initialize();
        $this->set_prefix('reservations_');
    }

    function delete_reservation($reservation)
    {
        $condition = new EqualityCondition(Reservation :: PROPERTY_ID, $reservation->get_id());
        $succes1 = $this->delete('reservation', $condition);

        $condition = new EqualityCondition(Subscription :: PROPERTY_RESERVATION_ID, $reservation->get_id());
        $succes2 = $this->delete('subscription', $condition);

        $succes = $succes1 & $succes2;

        return $succes;

    }

    function update_reservation($reservation)
    {
        $condition = new EqualityCondition(Reservation :: PROPERTY_ID, $reservation->get_id());
        return $this->update($reservation, $condition);
    }

    function create_reservation($reservation)
    {
        return $this->create($reservation);
    }

    function count_reservations($conditions = null)
    {
        return $this->count_objects('reservation', $conditions);
    }

    function retrieve_reservations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects('reservation', $condition, $offset, $count, $order_property);
    }

    function delete_category($category)
    {
        $condition = new EqualityCondition(Category :: PROPERTY_ID, $category->get_id());
        $succes1 = $this->delete('category', $condition);

        $this->clean_display_order($category);

        $condition = new EqualityCondition(Item :: PROPERTY_CATEGORY, $category->get_id());
        $succes2 = $this->delete('item', $condition);

        $succes = $succes1 & $succes2;

        return $succes;
    }

    function clean_display_order($category)
    {
        $query = 'UPDATE ' . $this->escape_table_name('category') . ' SET ' . $this->escape_column_name(Category :: PROPERTY_DISPLAY_ORDER) . '=' .
        		  $this->escape_column_name(Category :: PROPERTY_DISPLAY_ORDER) . '-1 WHERE ' .
        		  $this->escape_column_name(Category :: PROPERTY_DISPLAY_ORDER) . '>' . $this->quote($category->get_display_order()) . ' AND ' .
        		  $this->escape_column_name(Category :: PROPERTY_PARENT) . '=' . $this->quote($category->get_parent());
		$res = $this->query($query);
		$res->free();
    }

    function update_category($category)
    {
        $condition = new EqualityCondition(Category :: PROPERTY_ID, $category->get_id());
        return $this->update($category, $condition);
    }

    function create_category($category)
    {
        return $this->create($category);
    }

    function count_categories($conditions = null)
    {
        return $this->count_objects('category', $conditions);
    }

    function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects('category', $condition, $offset, $count, $order_property);
    }

    function delete_item($item)
    {
        $condition = new EqualityCondition(Item :: PROPERTY_ID, $item->get_id());
        $succes = $this->delete('item', $condition);

        $condition = new EqualityCondition(Reservation :: PROPERTY_ITEM, $item->get_id());
        //$succes2 = $this->delete('reservation', $condition);
        $reservations = $this->retrieve_reservations($condition);
        while ($reservation = $reservations->next_result())
            $succes &= $reservation->delete();

        return $succes;
    }

    function update_item($item)
    {
        $condition = new EqualityCondition(Item :: PROPERTY_ID, $item->get_id());
        return $this->update($item, $condition);
    }

    function create_item($item)
    {
        return $this->create($item);
    }

    function count_items($conditions = null)
    {
        return $this->count_objects('item', $conditions);
    }

    function retrieve_items($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects('item', $condition, $offset, $count, $order_property);
    }

    function delete_quota($quota)
    {
        $condition = new EqualityCondition(Quota :: PROPERTY_ID, $quota->get_id());
        return $this->delete('quota', $condition);
    }

    function update_quota($quota)
    {
        $condition = new EqualityCondition(Quota :: PROPERTY_ID, $quota->get_id());
        return $this->update($quota, $condition);
    }

    function create_quota($quota)
    {
        return $this->create($quota);
    }

    function count_quotas($conditions = null)
    {
        return $this->count_objects('quota', $conditions);
    }

    function retrieve_quotas($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects('quota', $condition, $offset, $count, $order_property);
    }

    function delete_subscription($subscription)
    {
        $condition = new EqualityCondition(Subscription :: PROPERTY_ID, $subscription->get_id());
        return $this->delete('subscription', $condition);
    }

    function delete_subscriptions($condition)
    {
        return $this->delete('subscription', $condition);
    }

    function create_subscription($subscription)
    {
        return $this->create($subscription);
    }

    function update_subscription($subscription)
    {
        $condition = new EqualityCondition(Subscription :: PROPERTY_ID, $subscription->get_id());
        return $this->update($subscription, $condition);
    }

    function count_subscriptions($conditions = null)
    {
        return $this->count_objects(Subscription :: get_table_name(), $conditions);
    }

    function retrieve_subscriptions($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects(Subscription :: get_table_name(), $condition, $offset, $count, $order_property);
    }

    function delete_subscription_user($subscription_user)
    {
        if ($subscription_user->get_subscription_id())
            $conditions[] = new EqualityCondition(SubscriptionUser :: PROPERTY_SUBSCRIPTION_ID, $subscription_user->get_subscription_id());
        if ($subscription_user->get_user_id())
            $conditions[] = new EqualityCondition(SubscriptionUser :: PROPERTY_ID, $subscription_user->get_user_id());

        $condition = new AndCondition($conditions);
        return $this->delete('subscription_user', $condition);
    }

    function create_subscription_user($subscription_user)
    {
        return $this->create($subscription_user);
    }

    function count_subscription_users($condition = null)
    {
        return $this->count_objects('subscription_user', $condition);
    }

    function retrieve_subscription_users($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects('subscription_user', $condition, $offset, $count, $order_property);
    }

    function select_next_display_order($parent_category_id)
    {
        $query = 'SELECT MAX(' . Category :: PROPERTY_DISPLAY_ORDER . ') AS do FROM ' . $this->escape_table_name('category');

        $condition = new EqualityCondition(Category :: PROPERTY_PARENT, $parent_category_id);

        if (isset($condition))
        {
            $translator = new ConditionTranslator($this);
            $query .= $translator->render_query($condition);
        }

        $res = $this->query($query);
        $record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
        $res->free();

        return $record[0] + 1;
    }

    /**
     * Function to check whether a column is a date column or not
     * @param String $name the column name
     * @return false (default value)
     */
    static function is_date_column($name)
    {
        return ($name == User :: PROPERTY_REGISTRATION_DATE);
    }

    function retrieve_weight_user_reservations_between($min_start, $max_start, $user_id, $quota_box)
    {
        $query = 'SELECT SUM(s.weight) FROM reservations_reservation r ';
        $query .= 'JOIN reservations_subscription s ON r.id = s.reservation_id ';
        $query .= 'WHERE ( ( (r.type=\'2\' AND r.start_date >= ' . $this->quote($min_start) . ' AND r.start_date < ' . $this->quote($max_start) . ') ';
        $query .= 'OR (r.type=\'1\' AND s.start_time >= ' . $this->quote($min_start) . ' AND s.start_time < ' . $this->quote($max_start) . ') ';
        $query .= ') AND s.user_id=' . $this->quote($user_id) . ' AND r.status=\'0\' AND s.status = \'0\' AND s.quota_box = ' . $this->quote($quota_box) . ')';

        $res = $this->query($query);
        $record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
        $res->free();

        return $record[0];

    }

    function delete_quota_box($quota_box)
    {
        $condition = new EqualityCondition(QuotaBox :: PROPERTY_ID, $quota_box->get_id());
        $this->delete_quota_from_quota_box($quota_box->get_id());
        return $this->delete('quota_box', $condition);
    }

    function update_quota_box($quota_box)
    {
        $condition = new EqualityCondition(QuotaBox :: PROPERTY_ID, $quota_box->get_id());
        return $this->update($quota_box, $condition);
    }

    function create_quota_box($quota_box)
    {
        return $this->create($quota_box);
    }

    function count_quota_boxes($conditions = null)
    {
        return $this->count_objects('quota_box', $conditions);
    }

    function retrieve_quota_boxes($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects('quota_box', $condition, $offset, $count, $order_property);
    }

    function create_quota_rel_quota_box($quota_rel_quota_box)
    {
        return $this->create($quota_rel_quota_box);
    }

    function delete_quota_rel_quota_box($quota_rel_quota_box)
    {
        $conditions[] = new EqualityCondition(QuotaRelQuotaBox :: PROPERTY_QUOTA_ID, $quota_rel_quota_box->get_quota_id());
        $conditions[] = new EqualityCondition(QuotaRelQuotaBox :: PROPERTY_QUOTA_BOX_ID, $quota_rel_quota_box->get_quota_box_id());
        $condition = new AndCondition($conditions);

        return $this->delete('quota_rel_quota_box', $condition);
    }

    function delete_quota_from_quota_box($quota_box_id)
    {
        $condition = new EqualityCondition(QuotaRelQuotaBox :: PROPERTY_QUOTA_BOX_ID, $quota_box_id);

        return $this->delete('quota_rel_quota_box', $condition);
    }

    function retrieve_quota_rel_quota_boxes($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects('quota_rel_quota_box', $condition, $offset, $count, $order_property);
    }

    function create_quota_box_rel_category($quota_rel_quota_box)
    {
        return $this->create($quota_rel_quota_box);
    }

    function delete_quota_box_rel_category($quota_box_rel_category)
    {
        $condition = new EqualityCondition(QuotaBoxRelCategory :: PROPERTY_ID, $quota_box_rel_category->get_id());
        $this->empty_quota_box_rel_category($quota_box_rel_category->get_id());

        return $this->delete('quota_box_rel_category', $condition);
    }

    function empty_quota_box_rel_category($quota_box_rel_category_id)
    {
        $condition = new EqualityCondition(QuotaBoxRelCategoryRelUser :: PROPERTY_QUOTA_BOX_REL_CATEGORY_ID, $quota_box_rel_category_id);
        $succes = $this->delete('quota_box_rel_category_rel_user', $condition);

        $condition = new EqualityCondition(QuotaBoxRelCategoryRelGroup :: PROPERTY_QUOTA_BOX_REL_CATEGORY_ID, $quota_box_rel_category_id);
        $succes &= $this->delete('quota_box_rel_category_rel_group', $condition);

        return $succes;
    }

    function retrieve_quota_box_rel_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects('quota_box_rel_category', $condition, $offset, $count, $order_property);
    }

    function count_quota_box_rel_categories($condition = null)
    {
        return $this->count_objects('quota_box_rel_category', $condition);
    }

    function create_quota_box_rel_category_rel_user($quota_box_rel_category_rel_user)
    {
        return $this->create($quota_box_rel_category_rel_user);
    }

    function delete_quota_box_rel_category_rel_user($quota_box_rel_category_rel_user)
    {
        $conditions[] = new EqualityCondition(QuotaBoxRelCategoryRelUser :: PROPERTY_QUOTA_BOX_REL_CATEGORY_ID, $quota_box_rel_category_rel_user->get_quota_box_rel_category_id());
        $conditions[] = new EqualityCondition(QuotaBoxRelCategoryRelUser :: PROPERTY_USER_ID, $quota_box_rel_category_rel_user->get_user_id());
        $condition = new AndCondition($conditions);

        return $this->delete('quota_box_rel_category_rel_user', $condition);
    }

    function retrieve_quota_box_rel_category_rel_users($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects('quota_box_rel_category_rel_user', $condition, $offset, $count, $order_property);
    }

    function count_quota_box_rel_category_rel_users($condition = null)
    {
        return $this->count_objects('quota_box_rel_category_rel_user', $condition);
    }

    function create_quota_box_rel_category_rel_group($quota_box_rel_category_rel_group)
    {
        return $this->create($quota_box_rel_category_rel_group);
    }

    function delete_quota_box_rel_category_rel_group($quota_box_rel_category_rel_group)
    {
        $conditions[] = new EqualityCondition(QuotaBoxRelCategoryRelGroup :: PROPERTY_QUOTA_BOX_REL_CATEGORY_ID, $quota_box_rel_category_rel_group->get_quota_box_rel_category_id());
        $conditions[] = new EqualityCondition(QuotaBoxRelCategoryRelGroup :: PROPERTY_GROUP_ID, $quota_box_rel_category_rel_group->get_group_id());
        $condition = new AndCondition($conditions);

        return $this->delete('quota_box_rel_category_rel_group', $condition);
    }

    function retrieve_quota_box_rel_category_rel_groups($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects('quota_box_rel_category_rel_group', $condition, $offset, $count, $order_property);
    }

    function count_quota_box_rel_category_rel_groups($condition = null)
    {
        return $this->count_objects('quota_box_rel_category_rel_group', $condition);
    }

    function create_overview_item($overview_item)
    {
        return $this->create($overview_item);
    }

    function empty_overview_for_user($user_id)
    {
        $condition = new EqualityCondition(OverviewItem :: PROPERTY_USER_ID, $user_id);
        return $this->delete('overview_item', $condition);
    }

    function retrieve_overview_items($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects('overview_item', $condition, $offset, $count, $order_property);
    }

    function count_overview_items($condition)
    {
        return $this->count_objects('overview_item', $condition);
    }

    function retrieve_quota_box_from_user_for_category($user_id, $category_id)
    {
        $query = 'SELECT quota_box_id FROM reservations_quota_box_rel_category WHERE category_id = ' . $this->quote($category_id) . ' AND id IN (SELECT quota_box_rel_category_id FROM
				  reservations_quota_box_rel_category_rel_user WHERE user_id = ' . $this->quote($user_id) . ');';

        $this->get_connection()->setLimit(intval(0), intval(1));
        $res = $this->query($query);
        $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$res->free();
        $id = $record['quota_box_id'];

        if (is_null($id))
        {
            //$groups = GroupDataManager :: get_instance()->retrieve_group_rel_users(new EqualityCondition(GroupRelUser :: PROPERTY_USER_ID, $user_id));
            $user = UserDataManager :: get_instance()->retrieve_user($user_id);
            $groups = $user->get_groups();
            if ($groups)
            {
                while ($group = $groups->next_result())
                {
                    $query = 'SELECT quota_box_id FROM reservations_quota_box_rel_category WHERE category_id = ' . $this->quote($category_id) . ' AND id IN (SELECT quota_box_rel_category_id FROM
					  		  reservations_quota_box_rel_category_rel_group WHERE group_id = ' . $this->quote($group->get_id()) . ');';

                    $this->get_connection()->setLimit(intval(0), intval(1));
                    $res = $this->query($query);
                    $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
					$res->free();

                    $id = $record['quota_box_id'];

                    if (! is_null($id))
                        return $id;
                }
            }
        }
        else
            return $id;

        return 0;
    }

    function count_overview_list_items($condition)
    {
        $sub_table = Subscription :: get_table_name();
        $sub_alias = $this->get_alias($sub_table);
        $sub_table = $this->escape_table_name($sub_table);

        $res_table = Reservation :: get_table_name();
        $res_alias = $this->get_alias($res_table);
        $res_table = $this->escape_table_name($res_table);

        $item_table = Item :: get_table_name();
        $item_alias = $this->get_alias($item_table);
        $item_table = $this->escape_table_name($item_table);

        $user_table = User :: get_table_name();
        $user_alias = $this->get_alias($user_table);
        $user_table = 'user_user';

        $query = 'SELECT COUNT(*) FROM ' . $sub_table . ' AS ' . $sub_alias . ' JOIN ' . $res_table . ' AS ' . $res_alias . ' ON ' . $sub_alias . '.reservation_id=' . $res_alias . '.id' . ' JOIN ' . $item_table . ' AS ' . $item_alias . ' ON ' . $res_alias . '.item_id=' . $item_alias . '.id' . ' JOIN ' . $user_table . ' AS ' . $user_alias . ' ON ' . $sub_alias . '.user_id=' . $user_alias . '.id';

        return $this->count_result_set($query, Subscription :: get_table_name(), $condition);
    }

    function retrieve_overview_list_items($condition, $offset, $count, $order_property)
    {
        $sub_table = Subscription :: get_table_name();
        $sub_alias = $this->get_alias($sub_table);
        $sub_table = $this->escape_table_name($sub_table);

        $res_table = Reservation :: get_table_name();
        $res_alias = $this->get_alias($res_table);
        $res_table = $this->escape_table_name($res_table);

        $item_table = Item :: get_table_name();
        $item_alias = $this->get_alias($item_table);
        $item_table = $this->escape_table_name($item_table);

        $user_table = User :: get_table_name();
        $user_alias = $this->get_alias($user_table);
        $user_table = 'user_user';

        $query = 'SELECT ' . $sub_alias . '.* FROM ' . $sub_table . ' AS ' . $sub_alias . ' JOIN ' . $res_table . ' AS ' . $res_alias . ' ON ' . $sub_alias . '.reservation_id=' . $res_alias . '.id' . ' JOIN ' . $item_table . ' AS ' . $item_alias . ' ON ' . $res_alias . '.item_id=' . $item_alias . '.id' . ' JOIN ' . $user_table . ' AS ' . $user_alias . ' ON ' . $sub_alias . '.user_id=' . $user_alias . '.id';

        return $this->retrieve_object_set($query, Subscription :: get_table_name(), $condition, $offset, $count, $order_property, Utilities :: underscores_to_camelcase(Subscription :: get_table_name()));
    }
}
?>