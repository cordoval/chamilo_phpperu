<?php
require_once dirname(__FILE__) . '/../cba_data_manager.class.php';
require_once dirname(__FILE__) . '/competency_category.class.php';
/**
 *	@author Nick Van Loocke
 */
class CompetencyCategoryManager extends CategoryManager
{

    function CompetencyCategoryManager($parent, $trail)
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
        return $adm->count_competency_categories($condition);
    }

    function retrieve_categories($condition, $offset, $count, $order_property)
    {
        $adm = CbaDataManager :: get_instance();
        return $adm->retrieve_competency_categories($condition, $offset, $count, $order_property);
    }

    function get_next_category_display_order($parent_id)
    {
        $condition = new EqualityCondition(PlatformCategory :: PROPERTY_PARENT, $parent_id);
        $sort = CbaDataManager :: get_instance()->retrieve_max_sort_value(CompetencyCategory :: get_table_name(), PlatformCategory :: PROPERTY_DISPLAY_ORDER, $condition);
        return $sort + 1;
    }
}
?>