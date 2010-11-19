<?php

namespace application\forum;

use common\libraries\EqualityCondition;
use common\extensions\category_manager\PlatformCategory;
use common\extensions\category_manager\CategoryManager;
/**
 * $Id: forum_publication_category_manager.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.forum.category_manager
 */
//require_once WebApplication :: get_application_class_lib_path('forum') . 'category_manager/forum_publication_category.class.php';

class ForumPublicationCategoryManager extends CategoryManager
{

    function __construct($parent, $trail)
    {
        parent :: __construct($parent, $trail, false);
    }

    function get_category()
    {
        return new ForumPublicationCategory();
    }

    function count_categories($condition)
    {
        return ForumDataManager :: get_instance()->count_forum_publication_categories($condition);
    }

    function retrieve_categories($condition, $offset, $count, $order_property)
    {
        return ForumDataManager :: get_instance()->retrieve_forum_publication_categories($condition, $offset, $count, $order_property, ForumPublicationCategory :: CLASS_NAME);
    }

    function get_next_category_display_order($parent_id)
    {
        $condition = new EqualityCondition(PlatformCategory :: PROPERTY_PARENT, $parent_id);
        $sort = ForumDataManager :: get_instance()->retrieve_max_sort_value(ForumPublicationCategory :: get_table_name(), PlatformCategory :: PROPERTY_DISPLAY_ORDER, $condition, ForumPublicationCategory :: CLASS_NAME);
        return $sort + 1;
    }
}
?>