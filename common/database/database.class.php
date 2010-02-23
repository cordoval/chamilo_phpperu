<?php
/**
 * $Id: database.class.php 229 2009-11-16 09:02:34Z scaramanga $
 * @package common.database
 */

/**
 * This class provides basic functionality for database connections
 * Create Table, Get next id, Insert, Update, Delete,
 * Select(with use of conditions), Count(with use of conditions)
 * @author Sven Vanpoucke
 */
class Database
{
    const ALIAS_MAX_SORT = 'max_sort';

    private $connection;
    private $prefix;
    private $aliases;

    /**
     * Constructor
     */
    function Database($aliases = array())
    {
        $this->aliases = $aliases;
        $this->initialize();
    }

    /**
     * Initialiser, creates the connection and sets the database to UTF8
     */
    function initialize()
    {
        $this->connection = Connection :: get_instance()->get_connection();
        $this->connection->setOption('debug_handler', array(get_class($this), 'debug'));
        $this->connection->setCharset('utf8');
    }

    /**
     * Returns the prefix
     * @return String the prefix
     */
    function get_prefix()
    {
        return $this->prefix;
    }

    /**
     * Sets the prefix
     * @param String $prefix
     */
    function set_prefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Returns the connection
     * @return Connection the connection
     */
    function get_connection()
    {
        return $this->connection;
    }

    /**
     * Sets the connection
     * @param Connection $connection
     */
    function set_connection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Debug function
     * Uncomment the lines if you want to debug
     */
    function debug()
    {
        $args = func_get_args();
        // Do something with the arguments
        if ($args[1] == 'query')
        {
            /*echo '<pre>';
		 	echo($args[2]);
		 	echo '</pre>';*/
        }
    }

    /**
     * Escapes a column name in accordance with the database type.
     * @param string $name The column name.
     * @param boolean $prefix_properties Whether or not to
     *                                                   prefix properties
     *                                                   to avoid collisions.
     * @return string The escaped column name.
     */
    function escape_column_name($name, $storage_unit = null)
    {
        $column_name = '';
        if (! is_null($storage_unit))
        {
            $column_name .= $storage_unit . '.';
        }

        return $column_name . $this->connection->quoteIdentifier($name);

    //        // Check whether the name contains a seperator, avoids notices.
    //        $contains_table_name = strpos($name, '.');
    //        if ($contains_table_name === false)
    //        {
    //            $table = $name;
    //            $column = null;
    //        }
    //        else
    //        {
    //            list($table, $column) = explode('.', $name, 2);
    //        }
    //
    //        $prefix = '';
    //        if (isset($column))
    //        {
    //            $prefix = $table . '.';
    //            $name = $column;
    //        }
    //        elseif ($storage_unit)
    //        {
    //            $prefix = $storage_unit . '.';
    //        }
    //        return $prefix . $this->connection->quoteIdentifier($name);
    }

    /**
     * Expands a table identifier to the real table name. Currently, this
     * method prefixes the given table name with the user-defined prefix, if
     * any.
     * @param string $name The table identifier.
     * @return string The actual table name.
     */
    function get_table_name($name)
    {
        if(strpos($_SERVER['PHP_SELF'], '/migration/index.php') !== false || strpos($_SERVER['PHP_SELF'], '_command_line_migration.php') !== false)
        { 
        	$dsn = $this->connection->getDSN('array');
        	return $dsn['database'] . '.' . $this->prefix . $name;
        }
        
        return $this->prefix . $name;
    }

    /**
     * Escapes a table name in accordance with the database type.
     * @param string $name The table identifier.
     * @return string The escaped table name.
     */
    function escape_table_name($name)
    {
    	if(strpos($_SERVER['PHP_SELF'], '/migration/index.php') !== false || strpos($_SERVER['PHP_SELF'], '_command_line_migration.php') !== false)
        { 
        	$dsn = $this->connection->getDSN('array');
        	return $dsn['database'] . '.' . $this->prefix . $name;
        }
        
        return $this->connection->quoteIdentifier($this->prefix . $name);
    }

    /**
     * Maps a record to an object
     * @param Record $record a record from the database
     * @param String $class Class to create new object
     * @return new object from type Class
     */
    function record_to_object($record, $class_name)
    {
        if (! is_array($record) || ! count($record))
        {
            throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
        }
        $default_properties = array();

        $object = new $class_name($default_properties);

        foreach ($object->get_default_property_names() as $property)
        {
            $default_properties[$property] = $record[$property];
        }

        $object->set_default_properties($default_properties);
        return $object;
    }

    /**
     * Creates a storage unit in the system
     * @param String $name the table name
     * @param Array $properties the table properties
     * @param Array $indexes the table indexes
     * @return true if the storage unit is succesfully created
     */
    function create_storage_unit($name, $properties, $indexes)
    {
        $name = $this->get_table_name($name);
        $this->connection->loadModule('Manager');
        $manager = $this->connection->manager;
        // If table allready exists -> drop it
        // @todo This should change: no automatic table drop but warning to user
        $tables = $manager->listTables();
        if (in_array($name, $tables))
        {
            $manager->dropTable($name);
        }
        $options['charset'] = 'utf8';
        $options['collate'] = 'utf8_unicode_ci';

        $result = $manager->createTable($name, $properties, $options);

        $constraint_table_alias = $this->get_constraint_name($name);

        if (! MDB2 :: isError($result))
        {
            foreach ($indexes as $index_name => $index_info)
            {
                if ($index_info['type'] == 'primary')
                {
                    $index_info['primary'] = 1;
                    $primary_result = $manager->createConstraint($name, $constraint_table_alias . '_' . $index_name . '_pk', $index_info);
                    if (MDB2 :: isError($primary_result))
                    {
                        echo '<pre>';
                        print_r($primary_result);
                        echo '</pre>';
                        return false;
                    }
                }
                elseif ($index_info['type'] == 'unique')
                {
                    $index_info['unique'] = 1;
                    $unique_result = $manager->createConstraint($name, $constraint_table_alias . '_' . $index_name . '_un', $index_info);
                    if (MDB2 :: isError($unique_result))
                    {
                        echo '<pre>';
                        print_r($unique_result);
                        echo '</pre>';
                        return false;
                    }
                }
                else
                {
                    $index_result = $manager->createIndex($name, $constraint_table_alias . '_' . $index_name . '_in', $index_info);
                    if (MDB2 :: isError($index_result))
                    {
                        echo '<pre>';
                        print_r($index_result);
                        echo '</pre>';
                        return false;
                    }
                }
            }
            return true;
        }
        else
        {
            echo '<pre>';
            print_r($result);
            echo '</pre>';
            return false;
        }
    }

    /**
     * Retrieves the next id for a given table
     * @param String $table_name
     * @return Int the id
     */
    function get_next_id($table_name)
    {
        $id = $this->connection->nextID($this->get_table_name($table_name));
        return $id;
    }

    function get_better_next_id($table_name, $column)
    {
        $this->connection->loadModule('Extended');
        return $this->connection->extended->getBeforeID($this->get_table_name($table_name), $column, true, true);
    }

    /**
     *
     */
    function create($object)
    {
        $object_table = $object->get_table_name();

        $props = array();
        foreach ($object->get_default_properties() as $key => $value)
        {
            $props[$this->escape_column_name($key)] = $value;
        }

        if (in_array('id', $object->get_default_property_names()))
        {
            $props[$this->escape_column_name('id')] = $this->get_better_next_id($object_table, 'id');
        }
        $this->connection->loadModule('Extended');

        if ($this->connection->extended->autoExecute($this->get_table_name($object_table), $props, MDB2_AUTOQUERY_INSERT))
        {
            if (in_array('id', $object->get_default_property_names()))
            {
                $object->set_id($this->connection->extended->getAfterID($props[$this->escape_column_name('id')], $this->get_table_name($object_table)));
            }
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Update functionality (can only be used when table has an ID)
     * @param Object $object the object that has to be updated
     * @param String $table_name the table name
     * @param Condition $condition The condition for the item that has to be updated
     * @return True if update is successfull
     */
    function update($object, $condition)
    {
        $object_table = $object->get_table_name();

        $props = array();
        foreach ($object->get_default_properties() as $key => $value)
        {
            $props[$this->escape_column_name($key)] = $value;
        }
        $this->connection->loadModule('Extended');
        $this->connection->extended->autoExecute($this->get_table_name($object_table), $props, MDB2_AUTOQUERY_UPDATE, $condition);

        return true;
    }

    function update_objects($table_name, $properties = array(), $condition, $offset = null, $max_objects = null, $order_by = array())
    {
        if (count($properties) > 0)
        {
        	$table_name_alias = $this->get_alias($table_name);
        	
            $query = 'UPDATE ' . $this->escape_table_name($table_name) . ' AS ' . $table_name_alias . ' SET ';

            $updates = array();
            foreach ($properties as $column => $property)
            {
                $updates[] = $this->escape_column_name($column, $table_name_alias) . '=' . $property;
            }

            $query .= implode(", ", $updates);

            if (isset($condition))
            {
                $translator = new ConditionTranslator($this, $this->get_alias($table_name));
                $query .= $translator->render_query($condition);
            }

            $orders = array();

            if (is_null($order_by))
            {
                $order_by = array();
            }
            elseif (! is_array($order_by))
            {
                $order_by = array($order_by);
            }

            foreach ($order_by as $order)
            {
                $orders[] = $this->escape_column_name($order->get_property(), ($order->alias_is_set() ? $order->get_alias() : $this->get_alias($table_name))) . ' ' . ($order->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
            }
            if (count($orders))
            {
                $query .= ' ORDER BY ' . implode(', ', $orders);
            }

            if ($max_objects > 0)
            {
                $query .= ' LIMIT ' . $max_objects;
            }

            $res = $this->query($query);
			
            if (MDB2 :: isError($res))
            {
                return false;
            }
            else
            {
                $res->free();
            	return true;
            }
        }
        else
        {
            return true;
        }
    }

    /**
     * Deletes an object from a table with a given condition
     * @param String $table_name
     * @param Condition $condition
     * @return true if deletion is successfull
     */
    function delete($table_name, $condition)
    {
        $query = 'DELETE FROM ' . $this->escape_table_name($table_name) . ' WHERE ' . $condition;
        $res = $this->query($query);

        if (MDB2 :: isError($res))
        {
            return false;
        }
        else
        {
        	$res->free();
            return true;
        }
    }

    /**
     * Deletes the objects of a given table
     * @param String $table_name
     * @param Condition $condition the condition
     * @return boolean
     */
    function delete_objects($table_name, $condition = null)
    {
        $query = 'DELETE ' . $this->get_alias($table_name) . '.* FROM ' . $this->escape_table_name($table_name) . ' AS ' . $this->get_alias($table_name);

        if (isset($condition))
        {
            $translator = new ConditionTranslator($this, $this->get_alias($table_name));
            $query .= $translator->render_query($condition);
        }

        $res = $this->query($query);
		
        if (MDB2 :: isError($res))
        {
            return false;
        }
        else
        {
            $res->free();
        	return true;
        }
    }

    /**
     * Drop a given storage unit
     * @param String $table_name
     * @return boolean
     */
    function drop_storage_unit($table_name)
    {
        $this->connection->loadModule('Manager');
        $manager = $this->connection->manager;

        $result = $manager->dropTable($this->escape_table_name($table_name));

        if (MDB2 :: isError($result))
        {
            return false;
        }
        else
        {
            $result->free();
        	return true;
        }
    }

    /**
     * Counts the objects of a table with a given condition
     * @param String $table_name
     * @param Condition $condition
     * return Int the number of objects
     */
    function count_objects($table_name, $condition = null)
    {
        $query = 'SELECT COUNT(*) FROM ' . $this->escape_table_name($table_name) . ' AS ' . $this->get_alias($table_name);

        return $this->count_result_set($query, $table_name, $condition);
    }

    function count_result_set($query, $table_name, $condition = null)
    {
        if (isset($condition))
        {
            $translator = new ConditionTranslator($this, $this->get_alias($table_name));
            $query .= $translator->render_query($condition);
        }

//        echo $query;
//        exit;

        $res = $this->query($query);

        if (MDB2 :: isError($res))
        {
            return false;
        }
        else
        {
            $record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
            $res->free();
            return $record[0];
        }
    }

    /**
     * Retrieves the objects of a given table
     * @param String $table_name
     * @param String $classname The name of the class where the object has to be mapped to
     * @param Condition $condition the condition
     * @param Int $offset the starting offset
     * @param Int $max_objects the max amount of objects to be retrieved
     * @param Array(String) $order_by the list of column names that the objects have to be ordered by
     * @param String $resultset - Optional, the resultset to map the items to
     * @return ResultSet
     */
    function retrieve_objects($table_name, $condition = null, $offset = null, $max_objects = null, $order_by = array(), $class_name = null)
    {
        $query = 'SELECT * FROM ' . $this->escape_table_name($table_name) . ' AS ' . $this->get_alias($table_name);
//        echo $query . '<br />';
        return $this->retrieve_object_set($query, $table_name, $condition, $offset, $max_objects, $order_by, $class_name);
    }

    function retrieve_result($query, $table_name, $condition = null, $offset = null, $max_objects = null, $order_by = array())
    {
        if (isset($condition))
        {
            $translator = new ConditionTranslator($this, $this->get_alias($table_name));
            $query .= $translator->render_query($condition);
        }

        $orders = array();

        //        dump('<strong>Statement</strong><br />' . $query . '<br /><br /><br />');
        //        dump($order_by);

        if (is_null($order_by))
        {
            $order_by = array();
        }
        elseif (! is_array($order_by))
        {
            $order_by = array($order_by);
        }

        foreach ($order_by as $order)
        {
            $orders[] = $this->escape_column_name($order->get_property(), ($order->alias_is_set() ? $order->get_alias() : $this->get_alias($table_name))) . ' ' . ($order->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
        }
        if (count($orders))
        {
            $query .= ' ORDER BY ' . implode(', ', $orders);
        }
        if ($max_objects < 0)
        {
            $max_objects = null;
        }

        $this->set_limit(intval($max_objects), intval($offset));

        return $this->query($query);
    }

    function retrieve_object_set($query, $table_name, $condition = null, $offset = null, $max_objects = null, $order_by = array(), $class_name = null)
    {
        $res = $this->retrieve_result($query, $table_name, $condition, $offset, $max_objects, $order_by);

        if (is_null($class_name))
        {
            $class_name = Utilities :: underscores_to_camelcase($table_name);
        }

        return new ObjectResultSet($this, $res, $class_name);
    }

    function retrieve_record_set($query, $table_name, $condition = null, $offset = null, $max_objects = null, $order_by = array())
    {
        return new RecordResultSet($this->retrieve_result($query, $table_name, $condition, $offset, $max_objects, $order_by));
    }

    function retrieve_max_sort_value($table_name, $column, $condition = null)
    {
        $query = 'SELECT MAX(' . $this->escape_column_name($column) . ') as ' . self :: ALIAS_MAX_SORT . ' FROM ' . $this->escape_table_name($table_name) . ' AS ' . $this->get_alias($table_name);

        if (isset($condition))
        {
            $translator = new ConditionTranslator($this, $this->get_alias($table_name));
            $query .= $translator->render_query($condition);
        }

        $res = $this->query($query);
        if ($res->numRows() >= 1)
        {
        	$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
        	$res->free();
            return $record[0];
        }
        else
        {
            $res->free();
        	return 0;
        }
    }

    function retrieve_next_sort_value($table_name, $column, $condition = null)
    {
        return $this->retrieve_max_sort_value($table_name, $column, $condition) + 1;
    }

    function truncate_storage_unit($table_name, $optimize = true)
    {
        $this->connection->loadModule('Manager');
        $manager = $this->connection->manager;
        if ($manager->truncateTable($this->escape_table_name($table_name)))
        {
            if ($optimize)
            {
                return $this->optimize_storage_unit($table_name);
            }
            else
            {
                return true;
            }
        }
        else
        {
            return false;
        }
    }

    function optimize_storage_unit($table_name)
    {
        $this->connection->loadModule('Manager');
        $manager = $this->connection->manager;
        if ($manager->vacuum($this->escape_table_name($table_name)))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function retrieve_record($table_name, $condition = null, $order_by = array())
    {
        $query = 'SELECT * FROM ' . $this->escape_table_name($table_name) . ' AS ' . $this->get_alias($table_name);
        return $this->retrieve_row($query, $table_name, $condition, $order_by);
    }

    function retrieve_row($query, $table_name, $condition = null, $order_by = array())
    {
        if (isset($condition))
        {
            $translator = new ConditionTranslator($this, $this->get_alias($table_name));
            $query .= $translator->render_query($condition);
        }

        $orders = array();

        foreach ($order_by as $order)
        {
            $orders[] = $this->escape_column_name($order->get_property(), ($order->alias_is_set() ? $order->get_alias() : $this->get_alias($table_name))) . ' ' . ($order->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
        }
        if (count($orders))
        {
            $query .= ' ORDER BY ' . implode(', ', $orders);
        }

        $this->set_limit(1);
        $res = $this->query($query);
        $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
        $res->free();

        if ($record)
        {
            return $record;
        }
        else
        {
            return false;
        }
    }

    function retrieve_object($table_name, $condition = null, $order_by = array(), $class_name = null)
    {
        $record = $this->retrieve_record($table_name, $condition, $order_by);

        if (is_null($class_name))
        {
            $class_name = Utilities :: underscores_to_camelcase($table_name);
        }

        if ($record)
        {
            return self :: record_to_object($record, $class_name);
        }
        else
        {
            return false;
        }
    }

    function retrieve_distinct($table_name, $column_name, $condition = null)
    {
        $query = 'SELECT DISTINCT(' . $this->escape_column_name($column_name) . ') FROM ' . $this->escape_table_name($table_name) . ' AS ' . $this->get_alias($table_name);

        if (isset($condition))
        {
            $translator = new ConditionTranslator($this, $this->get_alias($table_name));
            $query .= $translator->render_query($condition);
        }

        $res = $this->query($query);
        $distinct_elements = array();
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $distinct_elements[] = $record[$column_name];
        }
		$res->free();
        return $distinct_elements;
    }

    function count_distinct($table_name, $column_name, $condition = null)
    {
        $query = 'SELECT COUNT(DISTINCT(' . $this->escape_column_name($column_name) . ')) FROM ' . $this->escape_table_name($table_name) . ' AS ' . $this->get_alias($table_name);

        if (isset($condition))
        {
            $translator = new ConditionTranslator($this, $this->get_alias($table_name));
            $query .= $translator->render_query($condition);
        }

        $res = $this->query($query);
        $record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
        $res->free();
        return $record[0];
    }

    function get_alias($table_name)
    {
        if (!array_key_exists($table_name, $this->aliases))
        {
            $possible_name = substr($table_name, 0, 2) . substr($table_name, - 2);
            $index = 0;
            while (array_key_exists($possible_name, $this->aliases))
            {
                $possible_name = $possible_name . $index;
                $index = $index ++;
            }
            $this->aliases[$table_name] = $possible_name;
        }

        return $this->aliases[$table_name];
    }

    function get_constraint_name($name)
    {
        $possible_name = '';
        $parts = explode('_', $name);
        foreach($parts as $part)
        {
            $possible_name .= $part{0};
        }

        return $possible_name;
    }

    /**
     * Function to check whether a column is a date column or not
     * @param String $name the column name
     * @return false (default value)
     */
    static function is_date_column($name)
    {
        return false;
    }

    function quote($value, $type = null, $quote = true, $escape_wildcards = false)
    {
        return $this->connection->quote($value, $type, $quote, $escape_wildcards);
    }

    function query($query, $types = null, $result_class = true, $result_wrap_class = false)
    {
        return $this->connection->query($query, $types, $result_class, $result_wrap_class);
    }

    function set_limit($limit, $offset = null)
    {
        return $this->connection->setLimit($limit, $offset);
    }
}
?>