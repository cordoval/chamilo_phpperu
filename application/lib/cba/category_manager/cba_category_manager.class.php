<?php
require_once dirname(__FILE__) . '/cba_category.class.php';

class CbaCategoryManager extends CategoryManager
{

    function CbaCategoryManager($parent, $trail)
    {
        parent :: __construct($parent, $trail);
    }

    function get_category()
    {
        return new CbaCategory();
    }

    function count_categories($condition)
    {
        $wdm = CbaDataManager :: get_instance();

        if ($condition)
            $conditions[] = $condition;
        $conditions[] = new EqualityCondition(CbaCategory :: PROPERTY_USER_ID, $this->get_user_id());
        $condition = new AndCondition($conditions);

        return $wdm->count_categories($condition);
    }

    function retrieve_categories($condition, $offset, $count, $order_property)
    {
        $wdm = CbaDataManager :: get_instance();

        if ($condition)
        {
            $conditions[] = $condition;
        }

        $conditions[] = new EqualityCondition(CbaCategory :: PROPERTY_USER_ID, $this->get_user_id());
        $condition = new AndCondition($conditions);

        return $wdm->retrieve_categories($condition, $offset, $count, $order_property);
    }

    function get_next_category_display_order($parent_id)
    {
        $wdm = CbaDataManager :: get_instance();
        return $wdm->select_next_category_display_order($parent_id, Session :: get_user_id());
    }

    function allowed_to_delete_category($category_id)
    {
        $condition = new EqualityCondition(ContentObject :: PROPERTY_PARENT_ID, $category_id);
        $count = CbaDataManager :: get_instance()->count_content_objects($condition);

        return ($count == 0);
    }
}
?>