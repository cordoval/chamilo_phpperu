<?php
/**
 * $Id: reservations_manager_component.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.reservations_manager
 */

abstract class ReservationsManagerComponent
{
    /**
     * The number of components already instantiated
     */
    private static $component_count = 0;
    /**
     * The user manager in which this component is used
     */
    private $parent;
    /**
     * The id of this component
     */
    private $id;

    /**
     * Constructor
     * @param DCDAManager $user_manager The user manager which
     * provides this component
     */
    function ReservationsManagerComponent($parent)
    {
        $this->parent = $parent;
        $this->id = ++ self :: $component_count;
    }

    /**
     * @see DCDAManager::display_header()
     */
    function display_header($breadcrumbs = array (), $display_search = false)
    {
        $this->get_parent()->display_header($breadcrumbs, $display_search);
    }

    /**
     * @see DCDAManager::display_footer()
     */
    function display_footer()
    {
        $this->get_parent()->display_footer();
    }

    function display_search_form()
    {
        $this->get_parent()->display_search_form();
    }

    function get_search_condition()
    {
        return $this->get_parent()->get_search_condition();
    }

    /**
     * @see DCDAManager::display_message()
     */
    function display_message($message)
    {
        $this->get_parent()->display_message($message);
    }

    /**
     * @see DCDAManager::display_error_message()
     */
    function display_error_message($message)
    {
        $this->get_parent()->display_error_message($message);
    }

    /**
     * @see DCDAManager::display_error_page()
     */
    function display_error_page($message)
    {
        $this->get_parent()->display_error_page($message);
    }

    /**
     * @see DCDAManager::display_warning_page()
     */
    function display_warning_page($message)
    {
        $this->get_parent()->display_warning_page($message);
    }

    /**
     * @see DCDAManager::display_popup_form()
     */
    function display_popup_form($form_html)
    {
        $this->get_parent()->display_popup_form($form_html);
    }

    /**
     * Retrieve the user manager in which this component is active
     * @return DCDAManager
     */
    function get_parent()
    {
        return $this->parent;
    }

    /**
     * Retrieve the component id
     */
    function get_component_id()
    {
        return $this->id;
    }

    /**
     * @see DCDAManager::get_user
     */
    function get_user()
    {
        return $this->get_parent()->get_user();
    }

    /**
     * @see DCDAManager::get_id()
     */
    function get_user_id()
    {
        return $this->get_parent()->get_user_id();
    }

    /**
     * @see DCDAManager::get_parameters()
     */
    function get_parameters($include_search = false)
    {
        return $this->get_parent()->get_parameters($include_search);
    }

    /**
     * @see DCDAManager::get_parameter()
     */
    function get_parameter($name)
    {
        return $this->get_parent()->get_parameter($name);
    }

    /**
     * @see DCDAManager::set_parameter()
     */
    function set_parameter($name, $value)
    {
        $this->get_parent()->set_parameter($name, $value);
    }

    /**
     * @see DCDAManager::get_url()
     */
    function get_url($additional_parameters = array(), $include_search = false, $encode_entities = false)
    {
        return $this->get_parent()->get_url($additional_parameters, $include_search, $encode_entities);
    }

    /**
     * @see DCDAManager::get_link()
     */
    function get_link($parameters = array (), $encode = false)
    {
        return $this->get_parent()->get_link($parameters, $encode);
    }

    /**
     * @see DCDAManager::redirect()
     */
    function redirect($type = 'url', $message = null, $error_message = false, $extra_params = null)
    {
        return $this->get_parent()->redirect($type, $message, $error_message, $extra_params);
    }

    /**
     * @see DCDAManager::get_web_code_path()
     */
    function get_path($path_type)
    {
        return $this->get_parent()->get_path($path_type);
    }

    /**
     * Create a new user manager component
     * @param string $type The type of the component to create.
     * @param DCDAManager $user_manager The user manager in
     * which the created component will be used
     */
    static function factory($type, $parent)
    {
        $filename = dirname(__FILE__) . '/component/' . Utilities :: camelcase_to_underscores($type) . '.class.php';
        if (! file_exists($filename) || ! is_file($filename))
        {
            die('Failed to load "' . $type . '" component');
        }
        $class = 'ReservationsManager' . $type . 'Component';
        require_once $filename;
        return new $class($parent);
    }

    function has_right($type, $id, $right)
    {
        return $this->get_parent()->has_right($type, $id, $right);
    }

    function has_enough_credits_for($item, $start_date, $stop_date, $user_id)
    {
        return $this->get_parent()->has_enough_credits_for($item, $start_date, $stop_date, $user_id);
    }

    function count_items($condition = null)
    {
        return $this->get_parent()->count_items($condition);
    }

    function retrieve_items($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_items($condition, $offset, $count, $order_property);
    }

    function count_categories($condition = null)
    {
        return $this->get_parent()->count_categories($condition);
    }

    function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_categories($condition, $offset, $count, $order_property);
    }

    function count_reservations($condition = null)
    {
        return $this->get_parent()->count_reservations($condition);
    }

    function retrieve_reservations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_reservations($condition, $offset, $count, $order_property);
    }

    function count_subscriptions($condition = null)
    {
        return $this->get_parent()->count_subscriptions($condition);
    }

    function retrieve_subscriptions($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_subscriptions($condition, $offset, $count, $order_property);
    }

    function count_quotas($condition = null)
    {
        return $this->get_parent()->count_quotas($condition);
    }

    function retrieve_quotas($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_quotas($condition, $offset, $count, $order_property);
    }

    function count_quota_boxes($condition = null)
    {
        return $this->get_parent()->count_quota_boxes($condition);
    }

    function retrieve_quota_boxes($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_quota_boxes($condition, $offset, $count, $order_property);
    }

    function retrieve_quota_box_rel_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_quota_box_rel_categories($condition, $offset, $count, $order_property);
    }

    function count_quota_box_rel_categories($condition = null)
    {
        return $this->get_parent()->count_quota_box_rel_categories($condition);
    }

    function count_subscription_users($condition = null)
    {
        return $this->get_parent()->count_subscription_users($condition);
    }

    function retrieve_subscription_users($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_subscription_users($condition, $offset, $count, $order_property);
    }

    function retrieve_overview_items($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_overview_items($condition, $offset, $count, $order_property);
    }

    function count_overview_items($condition)
    {
        return $this->get_parent()->count_overview_items($condition);
    }

    function get_browse_categories_url($category_id = 0)
    {
        return $this->get_parent()->get_browse_categories_url($category_id);
    }

    function get_create_category_url($category_id)
    {
        return $this->get_parent()->get_create_category_url($category_id);
    }

    function get_update_category_url($category_id)
    {
        return $this->get_parent()->get_update_category_url($category_id);
    }

    function get_delete_category_url($category_id)
    {
        return $this->get_parent()->get_delete_category_url($category_id);
    }

    function get_move_category_url($category_id, $direction = 1)
    {
        return $this->get_parent()->get_move_category_url($category_id, $direction);
    }

    function get_blackout_category_url($category_id, $blackout)
    {
        return $this->get_parent()->get_blackout_category_url($category_id, $blackout);
    }

    function get_credit_category_url($category_id)
    {
        return $this->get_parent()->get_credit_category_url($category_id);
    }

    function get_browse_items_url($item_id = 0)
    {
        return $this->get_parent()->get_browse_items_url($item_id);
    }

    function get_create_item_url($cat_id)
    {
        return $this->get_parent()->get_create_item_url($cat_id);
    }

    function get_update_item_url($item_id, $cat_id = 0)
    {
        return $this->get_parent()->get_update_item_url($item_id, $cat_id);
    }

    function get_delete_item_url($item_id, $cat_id = 0)
    {
        return $this->get_parent()->get_delete_item_url($item_id, $cat_id);
    }

    function get_browse_reservations_url($item_id = 0)
    {
        return $this->get_parent()->get_browse_reservations_url($item_id);
    }

    function get_create_reservation_url($item_id)
    {
        return $this->get_parent()->get_create_reservation_url($item_id);
    }

    function get_update_reservation_url($reservation_id, $item_id = 0)
    {
        return $this->get_parent()->get_update_reservation_url($reservation_id, $item_id);
    }

    function get_delete_reservation_url($reservation_id, $item_id = 0)
    {
        return $this->get_parent()->get_delete_reservation_url($reservation_id, $item_id);
    }

    function get_browse_subscriptions_url($subscription_id = 0)
    {
        return $this->get_parent()->get_browse_subscriptions_url($subscription_id);
    }

    function get_create_subscription_url($item_id)
    {
        return $this->get_parent()->get_create_subscription_url($item_id);
    }

    function get_delete_subscription_url($subscription_id, $item_id = 0)
    {
        return $this->get_parent()->get_delete_subscription_url($subscription_id, $item_id);
    }

    function get_browse_quotas_url()
    {
        return $this->get_parent()->get_browse_quotas_url();
    }

    function get_create_quota_url()
    {
        return $this->get_parent()->get_create_quota_url();
    }

    function get_update_quota_url($quota_id)
    {
        return $this->get_parent()->get_update_quota_url($quota_id);
    }

    function get_delete_quota_url($quota_id)
    {
        return $this->get_parent()->get_delete_quota_url($quota_id);
    }

    function get_browse_ref_quotas_url($ref_id, $group)
    {
        return $this->get_parent()->get_browse_ref_quotas_url($ref_id, $group);
    }

    function get_create_ref_quota_url($ref_id, $group)
    {
        return $this->get_parent()->get_create_ref_quota_url($ref_id, $group);
    }

    function get_delete_ref_quota_url($quota_id, $ref_id, $group)
    {
        return $this->get_parent()->get_delete_ref_quota_url($quota_id, $ref_id, $group);
    }

    function get_admin_browse_subscription_url($reservation_id)
    {
        return $this->get_parent()->get_admin_browse_subscription_url($reservation_id);
    }

    function get_approve_subscription_url($subscription_id)
    {
        return $this->get_parent()->get_approve_subscription_url($subscription_id);
    }

    function get_subscription_user_browser_url($subscription_id)
    {
        return $this->get_parent()->get_subscription_user_browser_url($subscription_id);
    }

    function get_subscription_user_updater_url($subscription_id)
    {
        return $this->get_parent()->get_subscription_user_updater_url($subscription_id);
    }

    function get_modify_rights_url($type, $id)
    {
        return $this->get_parent()->get_modify_rights_url($type, $id);
    }

    function get_browse_quota_boxes_url()
    {
        return $this->get_parent()->get_browse_quota_boxes_url();
    }

    function get_create_quota_box_url()
    {
        return $this->get_parent()->get_create_quota_box_url();
    }

    function get_update_quota_box_url($quota_box_id)
    {
        return $this->get_parent()->get_update_quota_box_url($quota_box_id);
    }

    function get_delete_quota_box_url($quota_box_id)
    {
        return $this->get_parent()->get_delete_quota_box_url($quota_box_id);
    }

    function get_browse_category_quota_boxes_url($category_id)
    {
        return $this->get_parent()->get_browse_category_quota_boxes_url($category_id);
    }

    function get_create_category_quota_box_url($category_id)
    {
        return $this->get_parent()->get_create_category_quota_box_url($category_id);
    }

    function get_update_category_quota_box_url($category_quota_box_id)
    {
        return $this->get_parent()->get_update_category_quota_box_url($category_quota_box_id);
    }

    function get_delete_category_quota_box_url($category_quota_box_id, $category_id)
    {
        return $this->get_parent()->get_delete_category_quota_box_url($category_quota_box_id, $category_id);
    }

    function get_manage_overview_url()
    {
        return $this->get_parent()->get_manage_overview_url();
    }
}
?>