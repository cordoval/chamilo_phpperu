<?php
/**
 * $Id: database.class.php 232 2009-11-16 10:11:48Z vanpouckesven $
 * @package home.lib.data_manager
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

class DatabaseHomeDataManager extends HomeDataManager
{
    const ALIAS_TAB_TABLE = 't';
    const ALIAS_ROW_TABLE = 'r';
    const ALIAS_COLUMN_TABLE = 'c';
    const ALIAS_BLOCK_TABLE = 'b';
    const ALIAS_BLOCK_CONFIG_TABLE = 'bc';
    const ALIAS_MAX_SORT = 'max_sort';

    /**
     * The database connection.
     */
    private $database;

    function initialize()
    {
        $this->database = new Database();
        $this->database->set_prefix('home_');
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

    private static function is_home_row_column($name)
    {
        return HomeRow :: is_default_property_name($name); //|| $name == User :: PROPERTY_TYPE || $name == User :: PROPERTY_DISPLAY_ORDER_INDEX || $name == User :: PROPERTY_USER_ID;
    }

    private static function is_home_column_column($name)
    {
        return HomeColumn :: is_default_property_name($name); //|| $name == User :: PROPERTY_TYPE || $name == User :: PROPERTY_DISPLAY_ORDER_INDEX || $name == User :: PROPERTY_USER_ID;
    }

    private static function is_home_block_column($name)
    {
        return HomeBlock :: is_default_property_name($name); //|| $name == User :: PROPERTY_TYPE || $name == User :: PROPERTY_DISPLAY_ORDER_INDEX || $name == User :: PROPERTY_USER_ID;
    }

    function create_storage_unit($name, $properties, $indexes)
    {
        return $this->database->create_storage_unit($name, $properties, $indexes);
    }

    function count_home_rows($condition = null)
    {
        return $this->database->count_objects(HomeRow :: get_table_name(), $condition);
    }

    function count_home_columns($condition = null)
    {
        return $this->database->count_objects(HomeColumn :: get_table_name(), $condition);
    }

    function count_home_blocks($condition = null)
    {
        return $this->database->count_objects(HomeBlock :: get_table_name(), $condition);
    }

    function retrieve_home_rows($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        $order_by[] = new ObjectTableOrder(HomeRow :: PROPERTY_SORT);
        return $this->database->retrieve_objects(HomeRow :: get_table_name(), $condition, $offset, $max_objects, $order_by, HomeRow :: CLASS_NAME);
    }

    function retrieve_home_row($id)
    {
        $condition = new EqualityCondition(HomeRow :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(HomeRow :: get_table_name(), $condition, array(), HomeRow :: CLASS_NAME);
    }

    function retrieve_home_tabs($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        $order_by[] = new ObjectTableOrder(HomeTab :: PROPERTY_SORT);
        return $this->database->retrieve_objects(HomeTab :: get_table_name(), $condition, $offset, $max_objects, $order_by, HomeTab :: CLASS_NAME);
    }

    function retrieve_home_tab($id)
    {
        $condition = new EqualityCondition(HomeTab :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(HomeTab :: get_table_name(), $condition, array(), HomeTab :: CLASS_NAME);
    }

    function retrieve_home_tab_blocks($home_tab)
    {
        $query = 'SELECT * FROM ' . $this->database->escape_table_name(HomeBlock :: get_table_name()) . ' AS ' . self :: ALIAS_BLOCK_TABLE . ' WHERE ' . $this->database->escape_column_name(HomeBlock :: PROPERTY_COLUMN) . ' IN (SELECT ' . $this->database->escape_column_name(HomeColumn :: PROPERTY_ID) . ' FROM ' . $this->database->escape_table_name(HomeColumn :: get_table_name()) . ' AS ' . self :: ALIAS_COLUMN_TABLE . ' WHERE ' . $this->database->escape_column_name(HomeColumn :: PROPERTY_ROW) . ' IN (SELECT ' . $this->database->escape_column_name(HomeRow :: PROPERTY_ID) . ' FROM ' . $this->database->escape_table_name(HomeRow :: get_table_name()) . ' AS ' . self :: ALIAS_ROW_TABLE . ' WHERE ' . $this->database->escape_column_name(HomeRow :: PROPERTY_TAB) . ' = ' . $this->quote($home_tab->get_id()) . '))';
        $res = $this->query($query);
        return new ObjectResultSet($this->database, $res, HomeBlock :: CLASS_NAME);
    }

    function retrieve_home_columns($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        $order_by[] = new ObjectTableOrder(HomeColumn :: PROPERTY_SORT);
        return $this->database->retrieve_objects(HomeColumn :: get_table_name(), $condition, $offset, $max_objects, $order_by, HomeColumn :: CLASS_NAME);
    }

    function retrieve_home_column($id)
    {
        $condition = new EqualityCondition(HomeColumn :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(HomeColumn :: get_table_name(), $condition, array(), HomeColumn :: CLASS_NAME);
    }

    function retrieve_home_blocks($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        $order_by[] = new ObjectTableOrder(HomeBlock :: PROPERTY_SORT);
        return $this->database->retrieve_objects(HomeBlock :: get_table_name(), $condition, $offset, $max_objects, $order_by, HomeBlock :: CLASS_NAME);
    }

    function retrieve_home_block($id)
    {
        $condition = new EqualityCondition(HomeBlock :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(HomeBlock :: get_table_name(), $condition, array(), HomeBlock :: CLASS_NAME);
    }

    function retrieve_max_sort_value($table, $column, $condition = null)
    {
        return $this->database->retrieve_max_sort_value($table, $column, $condition);
    }

    function create_home_block($home_block)
    {
        return $this->database->create($home_block);
    }

    function create_home_block_config($home_block_config)
    {
        return $this->database->create($home_block_config);
    }

    function create_home_column($home_column)
    {
        return $this->database->create($home_column);
    }

    function create_home_row($home_row)
    {
        return $this->database->create($home_row);
    }

    function create_home_tab($home_tab)
    {
        return $this->database->create($home_tab);
    }

    function truncate_home($user_id)
    {
        $failures = 0;

        $condition = new EqualityCondition(HomeBlock :: PROPERTY_USER, $user_id);
        if (! $this->database->delete(HomeBlock :: get_table_name(), $condition))
        {
            $failures ++;
        }

        $condition = new EqualityCondition(HomeColumn :: PROPERTY_USER, $user_id);
        if (! $this->database->delete(HomeColumn :: get_table_name(), $condition))
        {
            $failures ++;
        }

        $condition = new EqualityCondition(HomeRow :: PROPERTY_USER, $user_id);
        if (! $this->database->delete(HomeRow :: get_table_name(), $condition))
        {
            $failures ++;
        }

        $condition = new EqualityCondition(HomeTab :: PROPERTY_USER, $user_id);
        if (! $this->database->delete(HomeTab :: get_table_name(), $condition))
        {
            $failures ++;
        }

        if ($failures == 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function update_home_block($home_block)
    {
        $old_home_block = $this->retrieve_home_block($home_block->get_id());

        if ($old_home_block->get_column() !== $home_block->get_column())
        {
            $condition = new EqualityCondition(HomeBlock :: PROPERTY_COLUMN, $home_block->get_column());
            $sort = $this->retrieve_max_sort_value(HomeBlock :: get_table_name(), HomeBlock :: PROPERTY_SORT, $condition);
            $home_block->set_sort($sort + 1);
        }

        $condition = new EqualityCondition(HomeBlock :: PROPERTY_ID, $home_block->get_id());
        $this->database->update($home_block, $condition);

        if ($old_home_block->get_column() !== $home_block->get_column())
        {
            $query = 'UPDATE ' . $this->database->escape_table_name(HomeBlock :: get_table_name()) . ' SET ' . $this->database->escape_column_name(HomeBlock :: PROPERTY_SORT) . ' = ' . $this->database->escape_column_name(HomeBlock :: PROPERTY_SORT) . ' - 1 WHERE ' .
            		 $this->database->escape_column_name(HomeBlock :: PROPERTY_SORT) . ' > ' . $this->quote($old_home_block->get_sort()) . ' AND ' .
            		 $this->database->escape_column_name(HomeBlock :: PROPERTY_COLUMN) . ' = ' . $this->quote($old_home_block->get_column());

        	$this->query($query);
        }

        return true;
    }

    function update_home_block_config($home_block_config)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(HomeBlockConfig :: PROPERTY_BLOCK_ID, $home_block_config->get_block_id());
        $conditions[] = new EqualityCondition(HomeBlockConfig :: PROPERTY_VARIABLE, $home_block_config->get_variable());
        $condition = new AndCondition($conditions);

        return $this->database->update($home_block_config, $condition);
    }

    function update_home_row($home_row)
    {
        $old_home_row = $this->retrieve_home_row($home_row->get_id());

        if ($old_home_row->get_tab() !== $home_row->get_tab())
        {
            $condition = new EqualityCondition(HomeRow :: PROPERTY_TAB, $home_row->get_tab());
            $sort = $this->retrieve_max_sort_value(HomeRow :: get_table_name(), HomeRow :: PROPERTY_SORT, $condition);
            $home_row->set_sort($sort + 1);
        }

        $condition = new EqualityCondition(HomeRow :: PROPERTY_ID, $home_row->get_id());
        $this->database->update($home_row, $condition);

        if ($old_home_row->get_tab() !== $home_row->get_tab())
        {
            $query = 'UPDATE ' . $this->database->escape_table_name(HomeRow :: get_table_name()) . ' SET ' .
            		 $this->database->escape_column_name(HomeRow :: PROPERTY_SORT) . ' = ' .
            		 $this->database->escape_column_name(HomeRow :: PROPERTY_SORT) . ' - 1 WHERE ' .
            	     $this->database->escape_column_name(HomeRow :: PROPERTY_SORT) . ' > ' . $this->quote($old_home_row->get_sort()) . ' AND ' .
            	     $this->database->escape_column_name(HomeRow :: PROPERTY_TAB) . ' = ' . $this->quote($old_home_row->get_tab());
            $this->query($query);
        }

        return true;
    }

    function update_home_column($home_column)
    {
        $old_home_column = $this->retrieve_home_column($home_column->get_id());

        if ($old_home_column->get_row() !== $home_column->get_row())
        {
            $condition = new EqualityCondition(HomeColumn :: PROPERTY_ROW, $home_column->get_row());
            $sort = $this->retrieve_max_sort_value(HomeColumn :: get_table_name(), HomeColumn :: PROPERTY_SORT, $condition);
            $home_column->set_sort($sort + 1);
        }

        $condition = new EqualityCondition(HomeColumn :: PROPERTY_ID, $home_column->get_id());
        $this->database->update($home_column, $condition);

        if ($old_home_column->get_row() !== $home_column->get_row())
        {
            $query = 'UPDATE ' . $this->database->escape_table_name(HomeColumn :: get_table_name()) . ' SET ' .
             	     $this->database->escape_column_name(HomeColumn :: PROPERTY_SORT) . ' = ' .
             	     $this->database->escape_column_name(HomeColumn :: PROPERTY_SORT) . ' - 1 WHERE ' .
             	     $this->database->escape_column_name(HomeColumn :: PROPERTY_SORT) . ' > ' . $this->quote($old_home_column->get_sort()) . ' AND ' .
             	     $this->database->escape_column_name(HomeColumn :: PROPERTY_ROW) . ' = ' . $this->quote($old_home_column->get_row());

            $this->query($query);
        }

        return true;
    }

    function update_home_tab($home_tab)
    {
        $condition = new EqualityCondition(HomeTab :: PROPERTY_ID, $home_tab->get_id());
        return $this->database->update($home_tab, $condition);
    }

    function retrieve_home_block_at_sort($parent, $sort, $direction)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(HomeBlock :: PROPERTY_COLUMN, $parent);

        if ($direction == 'up')
        {
            $conditions[] = new InequalityCondition(HomeBlock :: PROPERTY_SORT, InequalityCondition :: LESS_THAN, $sort);
            $order_direction = SORT_DESC;
        }
        elseif ($direction == 'down')
        {
            $conditions[] = new InequalityCondition(HomeBlock :: PROPERTY_SORT, InequalityCondition :: GREATER_THAN, $sort);
            $order_direction = SORT_ASC;
        }

        $condition = new AndCondition($conditions);

        return $this->database->retrieve_object(HomeBlock :: get_table_name(), $condition, array(new ObjectTableOrder(HomeBlock :: PROPERTY_SORT, $order_direction)), HomeBlock :: CLASS_NAME);
    }

    function retrieve_home_column_at_sort($parent, $sort, $direction)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(HomeColumn :: PROPERTY_ROW, $parent);

        if ($direction == 'up')
        {
            $conditions[] = new InequalityCondition(HomeColumn :: PROPERTY_SORT, InequalityCondition :: LESS_THAN, $sort);
            $order_direction = SORT_DESC;
        }
        elseif ($direction == 'down')
        {
            $conditions[] = new InequalityCondition(HomeColumn :: PROPERTY_SORT, InequalityCondition :: GREATER_THAN, $sort);
            $order_direction = SORT_ASC;
        }

        $condition = new AndCondition($conditions);

        return $this->database->retrieve_object(HomeColumn :: get_table_name(), $condition, array(new ObjectTableOrder(HomeColumn :: PROPERTY_SORT, $order_direction)), HomeColumn :: CLASS_NAME);
    }

    function retrieve_home_row_at_sort($parent, $sort, $direction)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(HomeRow :: PROPERTY_TAB, $parent);

        if ($direction == 'up')
        {
            $conditions[] = new InequalityCondition(HomeRow :: PROPERTY_SORT, InequalityCondition :: LESS_THAN, $sort);
            $order_direction = SORT_DESC;
        }
        elseif ($direction == 'down')
        {
            $conditions[] = new InequalityCondition(HomeRow :: PROPERTY_SORT, InequalityCondition :: GREATER_THAN, $sort);
            $order_direction = SORT_ASC;
        }

        $condition = new AndCondition($conditions);

        return $this->database->retrieve_object(HomeRow :: get_table_name(), $condition, array(new ObjectTableOrder(HomeRow :: PROPERTY_SORT, $order_direction)), HomeRow :: CLASS_NAME);
    }

    function retrieve_home_tab_at_sort($user, $sort, $direction)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(HomeTab :: PROPERTY_USER, $user);

        if ($direction == 'up')
        {
            $conditions[] = new InequalityCondition(HomeTab :: PROPERTY_SORT, InequalityCondition :: LESS_THAN, $sort);
            $order_direction = SORT_DESC;
        }
        elseif ($direction == 'down')
        {
            $conditions[] = new InequalityCondition(HomeTab :: PROPERTY_SORT, InequalityCondition :: GREATER_THAN, $sort);
            $order_direction = SORT_ASC;
        }

        $condition = new AndCondition($conditions);

        return $this->database->retrieve_object(HomeTab :: get_table_name(), $condition, array(new ObjectTableOrder(HomeTab :: PROPERTY_SORT, $order_direction)), HomeTab :: CLASS_NAME);
    }

    function delete_home_row($home_row)
    {
        $condition = new EqualityCondition(HomeColumn :: PROPERTY_ROW, $home_row->get_id());
        $columns = $this->retrieve_home_columns($condition);

        while ($column = $columns->next_result())
        {
            $this->delete_home_column($column);
        }

        $condition = new EqualityCondition(HomeRow :: PROPERTY_ID, $home_row->get_id());
        return $this->database->delete(HomeRow :: get_table_name(), $condition);
    }

    function delete_home_tab($home_tab)
    {
        $condition = new EqualityCondition(HomeRow :: PROPERTY_TAB, $home_tab->get_id());
        $rows = $this->retrieve_home_rows($condition);

        while ($row = $rows->next_result())
        {
            $this->delete_home_row($row);
        }

        $condition = new EqualityCondition(HomeTab :: PROPERTY_ID, $home_tab->get_id());
        return $this->database->delete(HomeTab :: get_table_name(), $condition);
    }

    function delete_home_column($home_column)
    {
        $condition = new EqualityCondition(HomeBlock :: PROPERTY_COLUMN, $home_column->get_id());
        $blocks = $this->retrieve_home_blocks($condition);

        while ($block = $blocks->next_result())
        {
            $this->delete_home_block($block);
        }

        $condition = new EqualityCondition(HomeColumn :: PROPERTY_ID, $home_column->get_id());
        return $this->database->delete(HomeColumn :: get_table_name(), $condition);
    }

    function delete_home_block($home_block)
    {
        if (! $this->delete_home_block_configs($home_block))
        {
            return false;
        }

        $condition = new EqualityCondition(HomeBlock :: PROPERTY_ID, $home_block->get_id());
        return $this->database->delete(HomeBlock :: get_table_name(), $condition);
    }

    function delete_home_block_config($home_block_config)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(HomeBlockConfig :: PROPERTY_BLOCK_ID, $home_block_config->get_block_id());
        $conditions[] = new EqualityCondition(HomeBlockConfig :: PROPERTY_VARIABLE, $home_block_config->get_variable());
        $condition = new AndCondition($conditions);

        return $this->database->delete(HomeBlockConfig :: get_table_name(), $condition);
    }

    function delete_home_block_configs($home_block)
    {
        $condition = new EqualityCondition(HomeBlockConfig :: PROPERTY_BLOCK_ID, $home_block->get_id());
        return $this->database->delete(HomeBlockConfig :: get_table_name(), $condition);
    }

    function retrieve_home_block_config($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(HomeBlockConfig :: get_table_name(), $condition, $offset, $max_objects, $order_by, HomeBlockConfig :: CLASS_NAME);
    }

    function count_home_block_config($condition = null)
    {
        return $this->database->count_objects(HomeBlockConfig :: get_table_name(), $condition);
    }
}
?>