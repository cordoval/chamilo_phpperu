<?php

namespace application\reservations;

use common\libraries\WebApplication;
use common\libraries\Theme;
use common\libraries\Translation;
use common\libraries\DynamicAction;
use common\libraries\Redirect;
use common\libraries\Path;
use common\libraries\Utilities;
/**
 * $Id: reservations_manager.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.reservations_manager
 */

require_once WebApplication :: get_application_class_lib_path('reservations') . 'reservations_manager/component/category_browser/category_browser_table.class.php';
require_once WebApplication :: get_application_class_lib_path('reservations') . 'reservations_manager/component/category_quota_box_browser/category_quota_box_browser_table.class.php';
require_once WebApplication :: get_application_class_lib_path('reservations') . 'reservations_manager/component/item_browser/item_browser_table.class.php';
require_once WebApplication :: get_application_class_lib_path('reservations') . 'reservations_manager/component/quota_box_browser/quota_box_browser_table.class.php';
require_once WebApplication :: get_application_class_lib_path('reservations') . 'reservations_manager/component/quota_browser/quota_browser_table.class.php';
require_once WebApplication :: get_application_class_lib_path('reservations') . 'reservations_manager/component/reservation_browser/reservation_browser_table.class.php';
require_once WebApplication :: get_application_class_lib_path('reservations') . 'reservations_manager/component/subscription_browser/subscription_browser_table.class.php';

class ReservationsManager extends WebApplication
{
    const APPLICATION_NAME = 'reservations';

    const PARAM_CATEGORY_ID = 'category_id';
    const PARAM_ITEM_ID = 'item_id';
    const PARAM_RESERVATION_ID = 'reservation_id';
    const PARAM_SUBSCRIPTION_ID = 'subscription_id';
    const PARAM_QUOTA_ID = 'quota_id';
    const PARAM_QUOTA_BOX_ID = 'quota_box_id';
    const PARAM_CATEGORY_QUOTA_BOX_ID = 'category_quota_box_id';
    const PARAM_REF_QUOTA_ID = 'ref_quota_id';
    const PARAM_REF_QUOTA_GROUP = 'ref_quota_group';
    const PARAM_BLACKOUT = 'blackout';

    const PARAM_REMOVE_SELECTED_ITEMS = 'remove_selected_items';
    const PARAM_REMOVE_SELECTED_CATEGORIES = 'remove_selected_categories';
    const PARAM_REMOVE_SELECTED_RESERVATIONS = 'remove_selected_reservations';
    const PARAM_REMOVE_SELECTED_SUBSCRIPTIONS = 'remove_selected_subscriptions';
    const PARAM_REMOVE_SELECTED_QUOTAS = 'remove_selected_quotas';
    const PARAM_REMOVE_SELECTED_REF_QUOTAS = 'remove_selected_ref_quotas';
    const PARAM_REMOVE_SELECTED_CATEGORY_QUOTA_BOXES = 'remove_selected_category_boxes';
    const PARAM_REMOVE_SELECTED_QUOTA_BOXES = 'remove_selected_quota_boxes';
    const PARAM_DIRECTION = 'direction';

    const ACTION_BROWSE_CATEGORIES = 'category_browser';
    const ACTION_ADMIN_BROWSE_CATEGORIES = 'admin_category_browser';
    const ACTION_CREATE_CATEGORY = 'category_creator';
    const ACTION_UPDATE_CATEGORY = 'category_updater';
    const ACTION_DELETE_CATEGORY = 'category_deleter';
    const ACTION_MOVE_CATEGORY = 'category_mover';
    const ACTION_BLACKOUT_CATEGORY = 'category_blackout';
    const ACTION_CREDIT_CATEGORY = 'category_credit';
    const ACTION_SEARCH_POOL = 'pool_searcher';

    const ACTION_BROWSE_ITEMS = 'item_browser';
    const ACTION_ADMIN_BROWSE_ITEMS = 'admin_item_browser';
    const ACTION_CREATE_ITEM = 'item_creator';
    const ACTION_UPDATE_ITEM = 'item_updater';
    const ACTION_DELETE_ITEM = 'item_deleter';

    const ACTION_BROWSE_RESERVATIONS = 'reservation_browser';
    const ACTION_ADMIN_BROWSE_RESERVATIONS = 'admin_reservation_browser';
    const ACTION_CREATE_RESERVATION = 'reservation_creator';
    const ACTION_UPDATE_RESERVATION = 'reservation_updater';
    const ACTION_DELETE_RESERVATION = 'reservation_deleter';

    const ACTION_BROWSE_SUBSCRIPTIONS = 'subscription_browser';
    const ACTION_ADMIN_BROWSE_SUBSCRIPTIONS = 'admin_subscription_browser';
    const ACTION_CREATE_SUBSCRIPTION = 'subscription_creator';
    const ACTION_DELETE_SUBSCRIPTION = 'subscription_deleter';
    const ACTION_APPROVE_SUBSCRIPTION = 'subscription_approver';
    const ACTION_BROWSE_SUBSCRIPTION_USERS = 'subscription_user_browser';
    const ACTION_UPDATE_SUBSCRIPTION_USER = 'subscription_user_updater';

    const ACTION_BROWSE_QUOTAS = 'quota_browser';
    const ACTION_CREATE_QUOTA = 'quota_creator';
    const ACTION_UPDATE_QUOTA = 'quota_updater';
    const ACTION_DELETE_QUOTA = 'quota_deleter';

    const ACTION_BROWSE_QUOTA_BOXES = 'quota_box_browser';
    const ACTION_CREATE_QUOTA_BOX = 'quota_box_creator';
    const ACTION_UPDATE_QUOTA_BOX = 'quota_box_updater';
    const ACTION_DELETE_QUOTA_BOX = 'quota_box_deleter';

    const ACTION_BROWSE_CATEGORY_QUOTA_BOXES = 'category_quota_box_browser';
    const ACTION_CREATE_CATEGORY_QUOTA_BOX = 'category_quota_box_creator';
    const ACTION_UPDATE_CATEGORY_QUOTA_BOX = 'category_quota_box_updater';
    const ACTION_DELETE_CATEGORY_QUOTA_BOX = 'category_quota_box_deleter';

    const ACTION_OVERVIEW = 'overview_browser';
    const ACTION_MANAGE_OVERVIEW = 'manage_overview';

    const ACTION_EDIT_RIGHTS = 'rights_editor';

    const DEFAULT_ACTION = self :: ACTION_BROWSE_ITEMS;

    private $parameters;
    private $search_parameters;
    private $search_form;
    private $user_id;
    private $user;
    private $dm;

    function __construct($user)
    {
        $this->dm = ReservationsDataManager :: get_instance();
        parent :: __construct($user);
    }

    function display_header($breadcrumb_trail)
    {
        parent :: display_header($breadcrumb_trail);
        echo $this->display_menu();
        echo '<div id="tool_browser_left">';
    }

    function display_footer()
    {
        echo '</div>';
        echo parent :: display_footer();
    }

    function display_menu()
    {
        $html = array();

        $html[] = '<div id="tool_bar" class="tool_bar tool_bar_left">';

        $html[] = '<div id="tool_bar_hide_container" class="hide">';
        $html[] = '<a id="tool_bar_hide" href="#"><img src="' . Theme :: get_common_image_path() . 'action_action_bar_left_hide.png" /></a>';
        $html[] = '<a id="tool_bar_show" href="#"><img src="' . Theme :: get_common_image_path() . 'action_action_bar_left_show.png" /></a>';
        $html[] = '</div>';

        $html[] = '<div class="tool_menu">';
        $html[] = '<ul>';

        $html[] = '<li class="tool_list_menu title">' . Translation :: get('Use') . '</li>';
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_browser.png)"><a href="' . $this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_SUBSCRIPTIONS)) . '">' . Translation :: get('MyReservations') . '</a></li>';
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_statistics.png)"><a href="' . $this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_OVERVIEW)) . '">' . Translation :: get('Statistics') . '</a></li>';

        $html[] = '<div class="splitter"></div>';

        $html[] = '<li class="tool_list_menu title" style="font-weight: bold">' . Translation :: get('Manage') . '</li>';

        $html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_item.png)"><a href="' . $this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_ITEMS)) . '">' . Translation :: get('ManageItems') . '</a></li>';
        if ($this->has_right(0, 0, ReservationsRights :: MANAGE_CATEGORIES_RIGHT))
        {
            $html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_category.png)"><a href="' . $this->get_url(array(
                    ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_CATEGORIES)) . '">' . Translation :: get('ManageCategories', null, Utilities :: COMMON_LIBRARIES) . '</a></li>';
            $html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_quota.png)"><a href="' . $this->get_url(array(
                    ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_QUOTAS)) . '">' . Translation :: get('ManageQuota') . '</a></li>';
            $html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_browser.png)"><a href="' . $this->get_url(array(
                    ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_QUOTA_BOXES)) . '">' . Translation :: get('ManageQuotaBoxes') . '</a></li>';
        }
        $html[] = '</ul>';
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'javascript/tool_bar.js' . '"></script>';
        $html[] = '<div class="clear"></div>';

        return implode($html, "\n");
    }

    function has_right($type, $id, $right)
    {
        //$location_id = ReservationsRights :: get_location_id_by_identifier($type, $id);
        return ReservationsRights :: is_allowed_in_reservations_subtree($right, $id, $type);
    }

    function has_enough_credits_for($item, $start_date, $stop_date, $user_id)
    {
        return ReservationsDataManager :: has_enough_credits_for($item, $start_date, $stop_date, $user_id);
    }

    /**
     * Gets the available links to display in the platform admin
     * @retun array of links and actions
     */
    public static function get_application_platform_admin_links()
    {
        $links = array();
        $links[] = new DynamicAction(Translation :: get('Categories'), Translation :: get('CategoriesDescription'), Theme :: get_image_path() . 'admin/category.png', Redirect :: get_link(self :: APPLICATION_NAME, array(
                self :: PARAM_ACTION => self :: ACTION_ADMIN_BROWSE_CATEGORIES)));
        $links[] = new DynamicAction(Translation :: get('Items'), Translation :: get('ItemsDescription'), Theme :: get_image_path() . 'admin/list.png', Redirect :: get_link(self :: APPLICATION_NAME, array(self :: PARAM_ACTION => self :: ACTION_ADMIN_BROWSE_ITEMS)));
        $links[] = new DynamicAction(Translation :: get('Quotas'), Translation :: get('QuotasDescription'), Theme :: get_image_path() . 'admin/list.png', Redirect :: get_link(self :: APPLICATION_NAME, array(self :: PARAM_ACTION => self :: ACTION_BROWSE_QUOTAS)));
        $links[] = new DynamicAction(Translation :: get('QuotaBoxes'), Translation :: get('QuotaBoxesDescription'), Theme :: get_image_path() . 'admin/list.png', Redirect :: get_link(self :: APPLICATION_NAME, array(
                self :: PARAM_ACTION => self :: ACTION_BROWSE_QUOTA_BOXES)));

        $info = parent :: get_application_platform_admin_links(self :: APPLICATION_NAME);
        $info['links'] = $links;
        return $info;
    }

    /**
     * DataManager Functions
     */
    function count_items($condition = null)
    {
        return $this->dm->count_items($condition);
    }

    function retrieve_items($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->dm->retrieve_items($condition, $offset, $count, $order_property);
    }

    function count_categories($condition = null)
    {
        return $this->dm->count_categories($condition);
    }

    function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->dm->retrieve_categories($condition, $offset, $count, $order_property);
    }

    function count_reservations($condition = null)
    {
        return $this->dm->count_reservations($condition);
    }

    function retrieve_reservations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->dm->retrieve_reservations($condition, $offset, $count, $order_property);
    }

    function count_subscriptions($condition = null)
    {
        return $this->dm->count_subscriptions($condition);
    }

    function retrieve_quotas($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->dm->retrieve_quotas($condition, $offset, $count, $order_property);
    }

    function count_quotas($condition = null)
    {
        return $this->dm->count_quotas($condition);
    }

    function retrieve_quota_boxes($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->dm->retrieve_quota_boxes($condition, $offset, $count, $order_property);
    }

    function count_quota_boxes($condition = null)
    {
        return $this->dm->count_quota_boxes($condition);
    }

    function retrieve_quota_box_rel_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->dm->retrieve_quota_box_rel_categories($condition, $offset, $count, $order_property);
    }

    function count_quota_box_rel_categories($condition = null)
    {
        return $this->dm->count_quota_box_rel_categories($condition);
    }

    function retrieve_subscriptions($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->dm->retrieve_subscriptions($condition, $offset, $count, $order_property);
    }

    function retrieve_subscription_users($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->dm->retrieve_subscription_users($condition, $offset, $count, $order_property);
    }

    function count_subscription_users($condition = null)
    {
        return $this->dm->count_subscription_users($condition);
    }

    function retrieve_overview_items($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->dm->retrieve_overview_items($condition, $offset, $count, $order_property);
    }

    function count_overview_items($condition)
    {
        return $this->dm->count_overview_items($condition);
    }

    /**
     * URL Functions
     */
    function get_browse_categories_url($category_id = 0)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_ADMIN_BROWSE_CATEGORIES, self :: PARAM_CATEGORY_ID => $category_id));
    }

    function get_create_category_url($category_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_CATEGORY, self :: PARAM_CATEGORY_ID => $category_id));
    }

    function get_update_category_url($category_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_CATEGORY, self :: PARAM_CATEGORY_ID => $category_id));
    }

    function get_delete_category_url($category_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_CATEGORY, self :: PARAM_CATEGORY_ID => $category_id));
    }

    function get_move_category_url($category_id, $direction = 1)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MOVE_CATEGORY, self :: PARAM_CATEGORY_ID => $category_id, self :: PARAM_DIRECTION => $direction));
    }

    function get_blackout_category_url($category_id, $blackout)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BLACKOUT_CATEGORY, self :: PARAM_CATEGORY_ID => $category_id, self :: PARAM_BLACKOUT => $blackout));
    }

    function get_credit_category_url($category_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREDIT_CATEGORY, self :: PARAM_CATEGORY_ID => $category_id));
    }

    function get_browse_items_url($item_id = 0)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ITEMS, self :: PARAM_ITEM_ID => $item_id));
    }

    function get_create_item_url($cat_id = 0)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_ITEM, self :: PARAM_CATEGORY_ID => $cat_id));
    }

    function get_update_item_url($item_id, $cat_id = 0)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_ITEM, self :: PARAM_ITEM_ID => $item_id, self :: PARAM_CATEGORY_ID => $cat_id));
    }

    function get_delete_item_url($item_id, $cat_id = 0)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_ITEM, self :: PARAM_ITEM_ID => $item_id, self :: PARAM_CATEGORY_ID => $cat_id));
    }

    function get_browse_reservations_url($item_id = 0)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_RESERVATIONS, self :: PARAM_ITEM_ID => $item_id));
    }

    function get_create_reservation_url($item_id = 0)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_RESERVATION, self :: PARAM_ITEM_ID => $item_id));
    }

    function get_update_reservation_url($reservation_id, $item_id = 0)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_RESERVATION, self :: PARAM_RESERVATION_ID => $reservation_id, self :: PARAM_ITEM_ID => $item_id));
    }

    function get_delete_reservation_url($reservation_id, $item_id = 0)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_RESERVATION, self :: PARAM_RESERVATION_ID => $reservation_id, self :: PARAM_ITEM_ID => $item_id));
    }

    function get_browse_subscriptions_url($reservation_id = 0)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SUBSCRIPTIONS, self :: PARAM_ITEM_ID => $reservation_id));
    }

    function get_create_subscription_url($item_id = 0)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_SUBSCRIPTION, self :: PARAM_ITEM_ID => $item_id));
    }

    function get_delete_subscription_url($subscription_id, $item_id = 0)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_SUBSCRIPTION, self :: PARAM_SUBSCRIPTION_ID => $subscription_id, self :: PARAM_ITEM_ID => $item_id));
    }

    function get_browse_quotas_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_QUOTAS));
    }

    function get_create_quota_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_QUOTA));
    }

    function get_update_quota_url($quota_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_QUOTA, self :: PARAM_QUOTA_ID => $quota_id));
    }

    function get_delete_quota_url($quota_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_QUOTA, self :: PARAM_QUOTA_ID => $quota_id));
    }

    function get_browse_ref_quotas_url($ref_id, $group)
    {
        return $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_REF_QUOTAS, self :: PARAM_REF_QUOTA_ID => $ref_id, self :: PARAM_REF_QUOTA_GROUP => $group));
    }

    function get_create_ref_quota_url($ref_id, $group)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_REF_QUOTA, self :: PARAM_REF_QUOTA_ID => $ref_id, self :: PARAM_REF_QUOTA_GROUP => $group));
    }

    function get_delete_ref_quota_url($quota_id, $ref_id, $group)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_REF_QUOTA, self :: PARAM_REF_QUOTA_ID => $ref_id, self :: PARAM_QUOTA_ID => $quota_id, self :: PARAM_REF_QUOTA_GROUP => $group));
    }

    function get_admin_browse_subscription_url($reservation_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_ADMIN_BROWSE_SUBSCRIPTIONS, self :: PARAM_RESERVATION_ID => $reservation_id));
    }

    function get_approve_subscription_url($subscription_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_APPROVE_SUBSCRIPTION, self :: PARAM_SUBSCRIPTION_ID => $subscription_id));
    }

    function get_subscription_user_browser_url($subscription_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SUBSCRIPTION_USERS, self :: PARAM_SUBSCRIPTION_ID => $subscription_id));
    }

    function get_subscription_user_updater_url($subscription_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_SUBSCRIPTION_USER, self :: PARAM_SUBSCRIPTION_ID => $subscription_id));
    }

    function get_modify_rights_url($type, $id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_RIGHTS, 'type' => $type, 'id' => $id));
    }

    function get_browse_quota_boxes_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_QUOTA_BOXES));
    }

    function get_create_quota_box_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_QUOTA_BOX));
    }

    function get_update_quota_box_url($quota_box_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_QUOTA_BOX, self :: PARAM_QUOTA_BOX_ID => $quota_box_id));
    }

    function get_delete_quota_box_url($quota_box_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_QUOTA_BOX, self :: PARAM_QUOTA_BOX_ID => $quota_box_id));
    }

    function get_browse_category_quota_boxes_url($category_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CATEGORY_QUOTA_BOXES, self :: PARAM_CATEGORY_ID => $category_id));
    }

    function get_create_category_quota_box_url($category_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_CATEGORY_QUOTA_BOX, self :: PARAM_CATEGORY_ID => $category_id));
    }

    function get_update_category_quota_box_url($category_quota_box_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_CATEGORY_QUOTA_BOX, self :: PARAM_CATEGORY_QUOTA_BOX_ID => $category_quota_box_id));
    }

    function get_delete_category_quota_box_url($category_quota_box_id, $category_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_CATEGORY_QUOTA_BOX, self :: PARAM_CATEGORY_QUOTA_BOX_ID => $category_quota_box_id, self :: PARAM_CATEGORY_ID => $category_id));
    }

    function get_manage_overview_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGE_OVERVIEW));
    }

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: APPLICATION_NAME
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: APPLICATION_NAME in the context of this class
     * - YourApplicationManager :: APPLICATION_NAME in all other application classes
     */
    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourApplicationManager :: DEFAULT_ACTION in all other application classes
     */
    function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }
}
?>