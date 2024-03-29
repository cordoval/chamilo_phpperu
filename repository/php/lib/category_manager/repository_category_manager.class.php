<?php
namespace repository;

use common\libraries\EqualityCondition;
use common\libraries\Session;
use common\libraries\AndCondition;

use common\extensions\category_manager\CategoryManager;

/**
 * $Id: repository_category_manager.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package repository.lib.category_manager
 */
require_once dirname(__FILE__) . '/repository_category.class.php';

class RepositoryCategoryManager extends CategoryManager
{

    function __construct($parent, $trail)
    {
        parent :: __construct($parent, $trail);
    }

    function get_category()
    {
        return new RepositoryCategory();
    }

    function count_categories($condition)
    {
        $wdm = RepositoryDataManager :: get_instance();

        if ($condition)
            $conditions[] = $condition;
        $conditions[] = new EqualityCondition(RepositoryCategory :: PROPERTY_USER_ID, $this->get_user_id());
        $condition = new AndCondition($conditions);

        return $wdm->count_categories($condition);
    }

    function retrieve_categories($condition, $offset, $count, $order_property)
    {
        $wdm = RepositoryDataManager :: get_instance();

        if ($condition)
        {
            $conditions[] = $condition;
        }

        $conditions[] = new EqualityCondition(RepositoryCategory :: PROPERTY_USER_ID, $this->get_user_id());
        $condition = new AndCondition($conditions);
        return $wdm->retrieve_categories($condition, $offset, $count, $order_property);
    }

    function get_next_category_display_order($parent_id)
    {
        $wdm = RepositoryDataManager :: get_instance();
        return $wdm->select_next_category_display_order($parent_id, Session :: get_user_id());
    }

    function allowed_to_delete_category($category_id)
    {
        $conditions = array();
    	$conditions[] = new EqualityCondition(ContentObject :: PROPERTY_PARENT_ID, $category_id);
        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_STATE, ContentObject :: STATE_NORMAL);
        $condition = new AndCondition($conditions);
        $object_count = RepositoryDataManager :: get_instance()->count_content_objects($condition);

        $condition = new EqualityCondition(RepositoryCategory :: PROPERTY_PARENT, $category_id);
        $category_count = RepositoryDataManager :: get_instance()->count_categories($condition);

        return (($object_count == 0) && ($category_count == 0));
    }
}
?>