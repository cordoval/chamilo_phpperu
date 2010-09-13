<?php
/**
 * $Id: weblcms_category_manager.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.category_manager
 */
require_once dirname(__FILE__) . '/../weblcms_data_manager.class.php';
require_once dirname(__FILE__) . '/course_category.class.php';

class WeblcmsCategoryManager extends CategoryManager
{

    function WeblcmsCategoryManager($parent)
    {
        $trail = BreadcrumbTrail :: get_instance();
        
        parent :: __construct($parent, $trail);
    }

    function get_category()
    {
        return new CourseCategory();
    }

    function get_category_form()
    {
        return new WeblcmsCategoryForm();
    }

    function count_categories($condition)
    {
        $wdm = WeblcmsDataManager :: get_instance();
        return $wdm->count_categories($condition);
    }

    function retrieve_categories($condition, $offset, $count, $order_property)
    {
        $wdm = WeblcmsDataManager :: get_instance();
        return $wdm->retrieve_categories($condition, $offset, $count, $order_property);
    }

    function get_next_category_display_order($parent_id)
    {
        $wdm = WeblcmsDataManager :: get_instance();
        
        $condition = new EqualityCondition(CourseCategory :: PROPERTY_PARENT, $parent_id);
        $sort = $wdm->retrieve_max_sort_value(CourseCategory :: get_table_name(), CourseCategory :: PROPERTY_DISPLAY_ORDER, $condition);
        
        return $sort + 1;
    }

    
}
?>