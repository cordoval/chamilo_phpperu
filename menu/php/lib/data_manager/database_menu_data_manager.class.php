<?php
namespace menu;

use common\libraries\Database;
use common\libraries\EqualityCondition;
use common\libraries\InequalityCondition;
use common\libraries\AndCondition;

/**
 * $Id: database_menu_data_manager.class.php 232 2009-11-16 10:11:48Z vanpouckesven $
 * @package menu.lib.data_manager
 */
require_once 'MDB2.php';
require_once dirname(__FILE__) . '/../menu_data_manager_interface.class.php';

/**
==============================================================================
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *	@author Tim De Pauw
 *	@author Bart Mollet
==============================================================================
 */

class DatabaseMenuDataManager extends Database implements MenuDataManagerInterface
{
    function initialize()
    {
        parent :: initialize();
        $this->set_prefix('menu_');
    }

    function count_navigation_items($condition = null)
    {
        return $this->count_objects(NavigationItem :: get_table_name(), $condition);
    }

    function retrieve_navigation_items($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(NavigationItem :: get_table_name(), $condition, $offset, $max_objects, $order_by, NavigationItem :: CLASS_NAME);
    }

    function retrieve_navigation_item($id)
    {
        $condition = new EqualityCondition(NavigationItem :: PROPERTY_ID, $id);
        return $this->retrieve_object(NavigationItem :: get_table_name(), $condition, array(), NavigationItem :: CLASS_NAME);
    }

    function retrieve_navigation_item_at_sort($parent, $sort, $direction)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(NavigationItem :: PROPERTY_CATEGORY, $parent);

        if ($direction == 'up')
        {
            $conditions[] = new InequalityCondition(NavigationItem :: PROPERTY_SORT, InequalityCondition :: LESS_THAN, $sort);
            $order_direction = SORT_DESC;
        }
        elseif ($direction == 'down')
        {
            $conditions[] = new InequalityCondition(NavigationItem :: PROPERTY_SORT, InequalityCondition :: GREATER_THAN, $sort);
            $order_direction = SORT_ASC;
        }

        $condition = new AndCondition($conditions);
        $order[] = new ObjectTableOrder(NavigationItem :: PROPERTY_SORT, $order_direction);

        return $this->retrieve_object(NavigationItem :: get_table_name(), $condition, $order, NavigationItem :: CLASS_NAME);
    }

    function update_navigation_item($navigation_item)
    {
        $old_navigation_item = $this->retrieve_navigation_item($navigation_item->get_id());

        if ($old_navigation_item->get_category() !== $navigation_item->get_category())
        {
            $condition = new EqualityCondition(NavigationItem :: PROPERTY_CATEGORY, $navigation_item->get_category());
            $sort = $this->retrieve_max_sort_value(NavigationItem :: get_table_name(), NavigationItem :: PROPERTY_SORT, $condition);

            $navigation_item->set_sort($sort + 1);
        }

        $condition = new EqualityCondition(NavigationItem :: PROPERTY_ID, $navigation_item->get_id());
        $this->update($navigation_item, $condition);

        if ($old_navigation_item->get_category() !== $navigation_item->get_category())
        {
            $query = 'UPDATE ' . $this->escape_table_name(NavigationItem :: get_table_name()) . ' SET ' .
            		 $this->escape_column_name(NavigationItem :: PROPERTY_SORT) . ' = ' .
            		 $this->escape_column_name(NavigationItem :: PROPERTY_SORT) . ' - 1 WHERE ' .
            		 $this->escape_column_name(NavigationItem :: PROPERTY_SORT) . ' > ' . $this->quote($old_navigation_item->get_sort()) . ' AND ' .
            		 $this->escape_column_name(NavigationItem :: PROPERTY_CATEGORY) . ' = ' . $this->quote($old_navigation_item->get_category());

            $res = $this->query($query);
            $res->free();
        }

        return true;
    }

    function delete_navigation_item($navigation_item)
    {
        $condition = new EqualityCondition(NavigationItem :: PROPERTY_ID, $navigation_item->get_id());
        $succes = $this->delete(NavigationItem :: get_table_name(), $condition);

        $query = 'UPDATE ' . $this->escape_table_name(NavigationItem :: get_table_name()) . ' SET ' .
            		 $this->escape_column_name(NavigationItem :: PROPERTY_SORT) . ' = ' .
            		 $this->escape_column_name(NavigationItem :: PROPERTY_SORT) . ' - 1 WHERE ' .
            		 $this->escape_column_name(NavigationItem :: PROPERTY_SORT) . ' > ' . $this->quote($navigation_item->get_sort()) . ' AND ' .
            		 $this->escape_column_name(NavigationItem :: PROPERTY_CATEGORY) . ' = ' . $this->quote($navigation_item->get_category());

        $res = $this->query($query);
        $res->free();

        return $succes;
    }

    function create_navigation_item($navigation_item)
    {
        return $this->create($navigation_item);
    }

    function delete_navigation_items($condition = null)
    {
        return $this->delete_objects(NavigationItem :: get_table_name(), $condition);
    }
}
?>