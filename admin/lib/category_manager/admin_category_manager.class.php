<?php
/**
 * $Id: admin_category_manager.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package admin.lib.category_manager
 */

require_once(dirname(__FILE__) . '/admin_category.class.php');

class AdminCategoryManager extends CategoryManager
{

    function AdminCategoryManager($parent, $trail)
    {
        parent :: __construct($parent, $trail);
    }

    function get_category()
    {
        return new AdminCategory();
    }

    function count_categories($condition)
    {
        $wdm = AdminDataManager :: get_instance();
        return $wdm->count_categories($condition);
    }

    function retrieve_categories($condition, $offset, $count, $order_property)
    {
        $wdm = AdminDataManager :: get_instance();
        return $wdm->retrieve_categories($condition, $offset, $count, $order_property);
    }

    function get_next_category_display_order($parent_id)
    {
        $wdm = AdminDataManager :: get_instance();
        return $wdm->select_next_display_order($parent_id);
    }
}
?>