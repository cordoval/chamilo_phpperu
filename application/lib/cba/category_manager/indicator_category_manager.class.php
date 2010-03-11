<?php
require_once dirname(__FILE__) . '/../cba_data_manager.class.php';
require_once dirname(__FILE__) . '/indicator_category.class.php';
/**
 *	@author Nick Van Loocke
 */
class IndicatorCategoryManager extends CategoryManager
{

    function IndicatorCategoryManager($parent, $trail)
    {
        parent :: __construct($parent, $trail);
    }

    function get_category()
    {
        return new CompetencyCategory();
    }

    function count_categories($condition)
    {
        $adm = CbaDataManager :: get_instance();
        return $adm->count_indicator_categories($condition);
    }

    function retrieve_categories($condition, $offset, $count, $order_property)
    {
        $adm = CbaDataManager :: get_instance();
        return $adm->retrieve_indicator_categories($condition, $offset, $count, $order_property);
    }

    function get_next_category_display_order($parent_id)
    {
        $adm = CbaDataManager :: get_instance();
        return $adm->select_next_indicator_category_display_order($parent_id);
    }
}
?>