<?php
interface ReservationsDataManagerInterface
{

    function create_storage_unit($name, $properties, $indexes);

    function delete_reservation($reservation);

    function update_reservation($reservation);

    function create_reservation($reservation);

    function count_reservations($conditions = null);

    function retrieve_reservations($condition = null, $offset = null, $count = null, $order_property = null);

    function select_next_display_order($parent_category_id);

    function delete_category($category);

    function update_category($category);

    function create_category($category);

    function count_categories($conditions = null);

    function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null);

    function delete_item($item);

    function update_item($item);

    function create_item($item);

    function count_items($conditions = null);

    function retrieve_items($condition = null, $offset = null, $count = null, $order_property = null);

    function delete_quota($quota);

    function update_quota($quota);

    function create_quota($quota);

    function count_quotas($conditions = null);

    function retrieve_quotas($condition = null, $offset = null, $count = null, $order_property = null);

    function delete_subscription($subscription);

    function delete_subscriptions($condition);

    function create_subscription($subscription);

    function update_subscription($subscription);

    function count_subscriptions($conditions = null);

    function retrieve_subscriptions($condition = null, $offset = null, $count = null, $order_property = null);

    function delete_quota_box($quota_box);

    function update_quota_box($quota_box);

    function create_quota_box($quota_box);

    function count_quota_boxes($conditions = null);

    function retrieve_quota_boxes($condition = null, $offset = null, $count = null, $order_property = null);

    function create_quota_rel_quota_box($quota_rel_quota_box);

    function delete_quota_rel_quota_box($quota_rel_quota_box);

    function delete_quota_from_quota_box($quota_box_id);

    function retrieve_quota_rel_quota_boxes($condition = null, $offset = null, $count = null, $order_property = null);

    function create_quota_box_rel_category($quota_rel_quota_box);

    function delete_quota_box_rel_category($quota_box_rel_category);

    function empty_quota_box_rel_category($quota_box_rel_category_id);

    function retrieve_quota_box_rel_categories($condition = null, $offset = null, $count = null, $order_property = null);

    function count_quota_box_rel_categories($condition = null);

    function create_quota_box_rel_category_rel_user($quota_box_rel_category_rel_user);

    function delete_quota_box_rel_category_rel_user($quota_box_rel_category_rel_user);

    function retrieve_quota_box_rel_category_rel_users($condition = null, $offset = null, $count = null, $order_property = null);

    function create_quota_box_rel_category_rel_group($quota_box_rel_category_rel_group);

    function delete_quota_box_rel_category_rel_group($quota_box_rel_category_rel_group);

    function retrieve_quota_box_rel_category_rel_groups($condition = null, $offset = null, $count = null, $order_property = null);

    function create_overview_item($overview_item);

    function empty_overview_for_user($user_id);

    function retrieve_overview_items($condition = null, $offset = null, $count = null, $order_property = null);

    function count_overview_items($condition);
}
?>