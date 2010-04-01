<?php
/**
 * $Id: database_menu_data_manager.class.php 232 2009-11-16 10:11:48Z vanpouckesven $
 * @package menu.lib.data_manager
 */
require_once 'MDB2.php';

/**
==============================================================================
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *	@author Tim De Pauw
 *	@author Bart Mollet
==============================================================================
 */

class DatabaseMenuDataManager extends MenuDataManager
{
    const ALIAS_CATEGORY_TABLE = 'c';
    const ALIAS_ITEM_TABLE = 'i';
    const ALIAS_MAX_SORT = 'max_sort';

    /**
     * The database connection.
     */
    private $database;

    function initialize()
    {
        $this->database = new Database();
        $this->database->set_prefix('menu_');
    }

	function quote($value)
    {
    	return $this->database->quote($value);
    }

    function query($query)
    {
    	return $this->database->query($query);
    }

    function get_database()
    {
        return $this->database;
    }

    function create_storage_unit($name, $properties, $indexes)
    {
        return $this->database->create_storage_unit($name, $properties, $indexes);
    }

    function count_navigation_items($condition = null)
    {
        return $this->database->count_objects(NavigationItem :: get_table_name(), $condition);
    }

    function retrieve_navigation_items($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(NavigationItem :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_navigation_item($id)
    {
        $condition = new EqualityCondition(NavigationItem :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(NavigationItem :: get_table_name(), $condition);
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

        return $this->database->retrieve_object(NavigationItem :: get_table_name(), $condition, $order);
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
        $this->database->update($navigation_item, $condition);

        if ($old_navigation_item->get_category() !== $navigation_item->get_category())
        {
            $query = 'UPDATE ' . $this->database->escape_table_name(NavigationItem :: get_table_name()) . ' SET ' .
            		 $this->database->escape_column_name(NavigationItem :: PROPERTY_SORT) . ' = ' .
            		 $this->database->escape_column_name(NavigationItem :: PROPERTY_SORT) . ' - 1 WHERE ' .
            		 $this->database->escape_column_name(NavigationItem :: PROPERTY_SORT) . ' > ' . $this->quote($old_navigation_item->get_sort()) . ' AND ' .
            		 $this->database->escape_column_name(NavigationItem :: PROPERTY_CATEGORY) . ' = ' . $this->quote($old_navigation_item->get_category());

            $res = $this->query($query);
            $res->free();
        }

        return true;
    }

    function delete_navigation_item($navigation_item)
    {
        $condition = new EqualityCondition(NavigationItem :: PROPERTY_ID, $navigation_item->get_id());
        $succes = $this->database->delete(NavigationItem :: get_table_name(), $condition);
        
        $query = 'UPDATE ' . $this->database->escape_table_name(NavigationItem :: get_table_name()) . ' SET ' .
            		 $this->database->escape_column_name(NavigationItem :: PROPERTY_SORT) . ' = ' .
            		 $this->database->escape_column_name(NavigationItem :: PROPERTY_SORT) . ' - 1 WHERE ' .
            		 $this->database->escape_column_name(NavigationItem :: PROPERTY_SORT) . ' > ' . $this->quote($navigation_item->get_sort()) . ' AND ' .
            		 $this->database->escape_column_name(NavigationItem :: PROPERTY_CATEGORY) . ' = ' . $this->quote($navigation_item->get_category());

        $res = $this->query($query);
        $res->free();
        
        return $succes;
    }

    function retrieve_max_sort_value($table, $column, $condition = null)
    {
        return $this->database->retrieve_max_sort_value($table, $column, $condition);
    }

    function create_navigation_item($navigation_item)
    {
        return $this->database->create($navigation_item);
    }

    function delete_navigation_items($condition = null)
    {
        return $this->database->delete_objects(NavigationItem :: get_table_name(), $condition);
    }
}
?>