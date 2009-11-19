<?php
/**
 * $Id: database.class.php 230 2009-11-16 09:29:45Z vanpouckesven $
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
require_once 'MDB2.php';

/**
==============================================================================
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *	@author Tim De Pauw
 *	@author Bart Mollet
==============================================================================
 */

class DatabaseReservationsDataManager extends ReservationsDataManager
{
    /**
     * @var Database
     */
    private $db;

    /**
     * Initializes the connection
     */
    function initialize()
    {
        $this->db = new Database(array('category' => 'cat', 'item' => 'i', 'quota' => 'q', 'quota_box' => 'qb', 'quota_rel_quota_box' => 'qrqb', 'quota_box_rel_category' => 'qbrc', 'quota_box_rel_category_rel_user' => 'qbrcru', 'quota_box_rel_category_rel_group' => 'qbrcrg', 'reservation' => 'res', 'subscription' => 'sub', 'user_quota' => 'uq', 'subscription_user' => 'sru', 'overview_item' => 'ovi'));
        $this->db->set_prefix('reservations_');
    }

	function quote($value)
    {
    	return $this->db->quote($value);
    }
    
    function query($query)
    {
    	return $this->db->query($query);
    }
    
    // Inherited.
    function create_storage_unit($name, $properties, $indexes)
    {
        return $this->db->create_storage_unit($name, $properties, $indexes);
    }

    function get_next_reservation_id()
    {
        return $this->db->get_next_id('reservation');
    }

    function escape_table_name($name)
    {
        return $this->db->escape_table_name($name);
    }

    function delete_reservation($reservation)
    {
        $condition = new EqualityCondition(Reservation :: PROPERTY_ID, $reservation->get_id());
        $succes1 = $this->db->delete('reservation', $condition);

        $condition = new EqualityCondition(Subscription :: PROPERTY_RESERVATION_ID, $reservation->get_id());
        $succes2 = $this->db->delete('subscription', $condition);

        $succes = $succes1 & $succes2;

        return $succes;

    }

    function update_reservation($reservation)
    {
        $condition = new EqualityCondition(Reservation :: PROPERTY_ID, $reservation->get_id());
        return $this->db->update($reservation, $condition);
    }

    function create_reservation($reservation)
    {
        return $this->db->create($reservation);
    }

    function count_reservations($conditions = null)
    {
        return $this->db->count_objects('reservation', $conditions);
    }

    function retrieve_reservations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->db->retrieve_objects('reservation', $condition, $offset, $count, $order_property);
    }

    function get_next_category_id()
    {
        return $this->db->get_next_id('category');
    }

    function delete_category($category)
    {
        $condition = new EqualityCondition(Category :: PROPERTY_ID, $category->get_id());
        $succes1 = $this->db->delete('category', $condition);

        $this->clean_display_order($category);

        $condition = new EqualityCondition(Item :: PROPERTY_CATEGORY, $category->get_id());
        $succes2 = $this->db->delete('item', $condition);

        $succes = $succes1 & $succes2;

        return $succes;
    }

    function clean_display_order($category)
    {
        $query = 'UPDATE ' . $this->db->escape_table_name('category') . ' SET ' . $this->db->escape_column_name(Category :: PROPERTY_DISPLAY_ORDER) . '=' . 
        		  $this->db->escape_column_name(Category :: PROPERTY_DISPLAY_ORDER) . '-1 WHERE ' . 
        		  $this->db->escape_column_name(Category :: PROPERTY_DISPLAY_ORDER) . '>' . $this->quote($category->get_display_order()) . ' AND ' . 
        		  $this->db->escape_column_name(Category :: PROPERTY_PARENT) . '=' . $this->quote($category->get_parent());
		$this->query($query);
    }

    function update_category($category)
    {
        $condition = new EqualityCondition(Category :: PROPERTY_ID, $category->get_id());
        return $this->db->update($category, $condition);
    }

    function create_category($category)
    {
        return $this->db->create($category);
    }

    function count_categories($conditions = null)
    {
        return $this->db->count_objects('category', $conditions);
    }

    function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->db->retrieve_objects('category', $condition, $offset, $count, $order_property);
    }

    function get_next_item_id()
    {
        return $this->db->get_next_id('item');
    }

    function delete_item($item)
    {
        $condition = new EqualityCondition(Item :: PROPERTY_ID, $item->get_id());
        $succes = $this->db->delete('item', $condition);

        $condition = new EqualityCondition(Reservation :: PROPERTY_ITEM, $item->get_id());
        //$succes2 = $this->db->delete('reservation', $condition);
        $reservations = $this->retrieve_reservations($condition);
        while ($reservation = $reservations->next_result())
            $succes &= $reservation->delete();

        return $succes;
    }

    function update_item($item)
    {
        $condition = new EqualityCondition(Item :: PROPERTY_ID, $item->get_id());
        return $this->db->update($item, $condition);
    }

    function create_item($item)
    {
        return $this->db->create($item);
    }

    function count_items($conditions = null)
    {
        return $this->db->count_objects('item', $conditions);
    }

    function retrieve_items($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->db->retrieve_objects('item', $condition, $offset, $count, $order_property);
    }

    function get_next_quota_id()
    {
        return $this->db->get_next_id('quota');
    }

    function delete_quota($quota)
    {
        $condition = new EqualityCondition(Quota :: PROPERTY_ID, $quota->get_id());
        return $this->db->delete('quota', $condition);
    }

    function update_quota($quota)
    {
        $condition = new EqualityCondition(Quota :: PROPERTY_ID, $quota->get_id());
        return $this->db->update($quota, $condition);
    }

    function create_quota($quota)
    {
        return $this->db->create($quota);
    }

    function count_quotas($conditions = null)
    {
        return $this->db->count_objects('quota', $conditions);
    }

    function retrieve_quotas($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->db->retrieve_objects('quota', $condition, $offset, $count, $order_property);
    }

    function get_next_subscription_id()
    {
        return $this->db->get_next_id('subscription');
    }

    function delete_subscription($subscription)
    {
        $condition = new EqualityCondition(Subscription :: PROPERTY_ID, $subscription->get_id());
        return $this->db->delete('subscription', $condition);
    }

    function delete_subscriptions($condition)
    {
        return $this->db->delete('subscription', $condition);
    }

    function create_subscription($subscription)
    {
        return $this->db->create($subscription);
    }

    function update_subscription($subscription)
    {
        $condition = new EqualityCondition(Subscription :: PROPERTY_ID, $subscription->get_id());
        return $this->db->update($subscription, $condition);
    }

    function count_subscriptions($conditions = null)
    {
        return $this->db->count_objects('subscription', $conditions);
    }

    function retrieve_subscriptions($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->db->retrieve_objects('subscription', $condition, $offset, $count, $order_property);
    }

    function delete_subscription_user($subscription_user)
    {
        if ($subscription_user->get_subscription_id())
            $conditions[] = new EqualityCondition(SubscriptionUser :: PROPERTY_SUBSCRIPTION_ID, $subscription_user->get_subscription_id());
        if ($subscription_user->get_user_id())
            $conditions[] = new EqualityCondition(SubscriptionUser :: PROPERTY_USER_ID, $subscription_user->get_user_id());

        $condition = new AndCondition($conditions);
        return $this->db->delete('subscription_user', $condition);
    }

    function create_subscription_user($subscription_user)
    {
        return $this->db->create($subscription_user);
    }

    function count_subscription_users($condition = null)
    {
        return $this->db->count_objects('subscription_user', $condition);
    }

    function retrieve_subscription_users($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->db->retrieve_objects('subscription_user', $condition, $offset, $count, $order_property);
    }

    function select_next_display_order($parent_category_id)
    {
        $query = 'SELECT MAX(' . Category :: PROPERTY_DISPLAY_ORDER . ') AS do FROM ' . $this->db->escape_table_name('category');

        $condition = new EqualityCondition(Category :: PROPERTY_PARENT, $parent_category_id);

        if (isset($condition))
        {
            $translator = new ConditionTranslator($this->db);
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

    function get_next_quota_box_id()
    {
        return $this->db->get_next_id('quota_box');
    }

    function delete_quota_box($quota_box)
    {
        $condition = new EqualityCondition(QuotaBox :: PROPERTY_ID, $quota_box->get_id());
        $this->delete_quota_from_quota_box($quota_box->get_id());
        return $this->db->delete('quota_box', $condition);
    }

    function update_quota_box($quota_box)
    {
        $condition = new EqualityCondition(QuotaBox :: PROPERTY_ID, $quota_box->get_id());
        return $this->db->update($quota_box, $condition);
    }

    function create_quota_box($quota_box)
    {
        return $this->db->create($quota_box);
    }

    function count_quota_boxes($conditions = null)
    {
        return $this->db->count_objects('quota_box', $conditions);
    }

    function retrieve_quota_boxes($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->db->retrieve_objects('quota_box', $condition, $offset, $count, $order_property);
    }

    function create_quota_rel_quota_box($quota_rel_quota_box)
    {
        return $this->db->create($quota_rel_quota_box);
    }

    function delete_quota_rel_quota_box($quota_rel_quota_box)
    {
        $conditions[] = new EqualityCondition(QuotaRelQuotaBox :: PROPERTY_QUOTA_ID, $quota_rel_quota_box->get_quota_id());
        $conditions[] = new EqualityCondition(QuotaRelQuotaBox :: PROPERTY_QUOTA_BOX_ID, $quota_rel_quota_box->get_quota_box_id());
        $condition = new AndCondition($conditions);

        return $this->db->delete('quota_rel_quota_box', $condition);
    }

    function delete_quota_from_quota_box($quota_box_id)
    {
        $condition = new EqualityCondition(QuotaRelQuotaBox :: PROPERTY_QUOTA_BOX_ID, $quota_box_id);

        return $this->db->delete('quota_rel_quota_box', $condition);
    }

    function retrieve_quota_rel_quota_boxes($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->db->retrieve_objects('quota_rel_quota_box', $condition, $offset, $count, $order_property);
    }

    function get_next_quota_box_rel_category_id()
    {
        return $this->db->get_next_id('quota_box_rel_category');
    }

    function create_quota_box_rel_category($quota_rel_quota_box)
    {
        return $this->db->create($quota_rel_quota_box);
    }

    function delete_quota_box_rel_category($quota_box_rel_category)
    {
        $condition = new EqualityCondition(QuotaBoxRelCategory :: PROPERTY_ID, $quota_box_rel_category->get_id());
        $this->empty_quota_box_rel_category($quota_box_rel_category->get_id());

        return $this->db->delete('quota_box_rel_category', $condition);
    }

    function empty_quota_box_rel_category($quota_box_rel_category_id)
    {
        $condition = new EqualityCondition(QuotaBoxRelCategoryRelUser :: PROPERTY_QUOTA_BOX_REL_CATEGORY_ID, $quota_box_rel_category_id);
        $succes = $this->db->delete('quota_box_rel_category_rel_user', $condition);

        $condition = new EqualityCondition(QuotaBoxRelCategoryRelGroup :: PROPERTY_QUOTA_BOX_REL_CATEGORY_ID, $quota_box_rel_category_id);
        $succes &= $this->db->delete('quota_box_rel_category_rel_group', $condition);

        return $succes;
    }

    function retrieve_quota_box_rel_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->db->retrieve_objects('quota_box_rel_category', $condition, $offset, $count, $order_property);
    }

    function count_quota_box_rel_categories($condition = null)
    {
        return $this->db->count_objects('quota_box_rel_category', $condition);
    }

    function create_quota_box_rel_category_rel_user($quota_box_rel_category_rel_user)
    {
        return $this->db->create($quota_box_rel_category_rel_user);
    }

    function delete_quota_box_rel_category_rel_user($quota_box_rel_category_rel_user)
    {
        $conditions[] = new EqualityCondition(QuotaBoxRelCategoryRelUser :: PROPERTY_QUOTA_BOX_REL_CATEGORY_ID, $quota_box_rel_category_rel_user->get_quota_box_rel_category_id());
        $conditions[] = new EqualityCondition(QuotaBoxRelCategoryRelUser :: PROPERTY_USER_ID, $quota_box_rel_category_rel_user->get_user_id());
        $condition = new AndCondition($conditions);

        return $this->db->delete('quota_box_rel_category_rel_user', $condition);
    }

    function retrieve_quota_box_rel_category_rel_users($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->db->retrieve_objects('quota_box_rel_category_rel_user', $condition, $offset, $count, $order_property);
    }

    function count_quota_box_rel_category_rel_users($condition = null)
    {
        return $this->db->count_objects('quota_box_rel_category_rel_user', $condition);
    }

    function create_quota_box_rel_category_rel_group($quota_box_rel_category_rel_group)
    {
        return $this->db->create($quota_box_rel_category_rel_group);
    }

    function delete_quota_box_rel_category_rel_group($quota_box_rel_category_rel_group)
    {
        $conditions[] = new EqualityCondition(QuotaBoxRelCategoryRelGroup :: PROPERTY_QUOTA_BOX_REL_CATEGORY_ID, $quota_box_rel_category_rel_group->get_quota_box_rel_category_id());
        $conditions[] = new EqualityCondition(QuotaBoxRelCategoryRelGroup :: PROPERTY_GROUP_ID, $quota_box_rel_category_rel_group->get_group_id());
        $condition = new AndCondition($conditions);

        return $this->db->delete('quota_box_rel_category_rel_group', $condition);
    }

    function retrieve_quota_box_rel_category_rel_groups($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->db->retrieve_objects('quota_box_rel_category_rel_group', $condition, $offset, $count, $order_property);
    }

    function count_quota_box_rel_category_rel_groups($condition = null)
    {
        return $this->db->count_objects('quota_box_rel_category_rel_group', $condition);
    }

    function create_overview_item($overview_item)
    {
        return $this->db->create($overview_item);
    }

    function empty_overview_for_user($user_id)
    {
        $condition = new EqualityCondition(OverviewItem :: PROPERTY_USER_ID, $user_id);
        return $this->db->delete('overview_item', $condition);
    }

    function retrieve_overview_items($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->db->retrieve_objects('overview_item', $condition, $offset, $count, $order_property);
    }

    function count_overview_items($condition)
    {
        return $this->db->count_objects('overview_item', $condition);
    }

    function retrieve_quota_box_from_user_for_category($user_id, $category_id)
    {
        $query = 'SELECT quota_box_id FROM reservations_quota_box_rel_category WHERE category_id = ' . $this->quote($category_id) . ' AND id IN (SELECT quota_box_rel_category_id FROM
				  reservations_quota_box_rel_category_rel_user WHERE user_id = ' . $this->quote($user_id) . ');';

        $this->db->get_connection()->setLimit(intval(0), intval(1));
        $res = $this->query($query);
        $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

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

                    $this->db->get_connection()->setLimit(intval(0), intval(1));
                    $res = $this->query($query);
                    $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

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
        $sub_alias = $this->db->get_alias($sub_table);
        $sub_table = $this->escape_table_name($sub_table);

        $res_table = Reservation :: get_table_name();
        $res_alias = $this->db->get_alias($res_table);
        $res_table = $this->escape_table_name($res_table);

        $item_table = Item :: get_table_name();
        $item_alias = $this->db->get_alias($item_table);
        $item_table = $this->escape_table_name($item_table);

        $user_table = User :: get_table_name();
        $user_alias = $this->db->get_alias($user_table);
        $user_table = 'user_user';

        $query = 'SELECT COUNT(*) FROM ' . $sub_table . ' AS ' . $sub_alias . ' JOIN ' . $res_table . ' AS ' . $res_alias . ' ON ' . $sub_alias . '.reservation_id=' . $res_alias . '.id' . ' JOIN ' . $item_table . ' AS ' . $item_alias . ' ON ' . $res_alias . '.item=' . $item_alias . '.id' . ' JOIN ' . $user_table . ' AS ' . $user_alias . ' ON ' . $sub_alias . '.user_id=' . $user_alias . '.user_id';

        $count = $this->db->count_result_set($query, Subscription :: get_table_name(), $condition);

        return $count;
    }

    function retrieve_overview_list_items($condition, $offset, $count, $order_property)
    {
        $sub_table = Subscription :: get_table_name();
        $sub_alias = $this->db->get_alias($sub_table);
        $sub_table = $this->escape_table_name($sub_table);

        $res_table = Reservation :: get_table_name();
        $res_alias = $this->db->get_alias($res_table);
        $res_table = $this->escape_table_name($res_table);

        $item_table = Item :: get_table_name();
        $item_alias = $this->db->get_alias($item_table);
        $item_table = $this->escape_table_name($item_table);

        $user_table = User :: get_table_name();
        $user_alias = $this->db->get_alias($user_table);
        $user_table = 'user_user';

        $query = 'SELECT ' . $sub_alias . '.* FROM ' . $sub_table . ' AS ' . $sub_alias . ' JOIN ' . $res_table . ' AS ' . $res_alias . ' ON ' . $sub_alias . '.reservation_id=' . $res_alias . '.id' . ' JOIN ' . $item_table . ' AS ' . $item_alias . ' ON ' . $res_alias . '.item=' . $item_alias . '.id' . ' JOIN ' . $user_table . ' AS ' . $user_alias . ' ON ' . $sub_alias . '.user_id=' . $user_alias . '.user_id';

        return $this->db->retrieve_object_set($query, Subscription :: get_table_name(), $condition, $offset, $count, $order_property, Utilities :: underscores_to_camelcase(Subscription :: get_table_name()));
    }

    function get_alias($name)
    {
        return $this->db->get_alias($name);
    }
}
?>