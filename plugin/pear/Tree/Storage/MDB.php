<?php
// LiveUser: A framework for authentication and authorization in PHP applications
// Copyright (C) 2002-2003 Markus Wolff
//
// This library is free software; you can redistribute it and/or
// modify it under the terms of the GNU Lesser General Public
// License as published by the Free Software Foundation; either
// version 2.1 of the License, or (at your option) any later version.
//
// This library is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
// Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public
// License along with this library; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

/**
 * MDB_Complex container for permission handling
 *
 * @package  LiveUser
 * @category authentication
 */

/**
 * Require parent class definition.
 */
require_once 'Tree/Storage/SQL.php';
require_once 'MDB.php';

/**
 * This is a PEAR::MDB backend driver for the LiveUser class.
 * A PEAR::MDB connection object can be passed to the constructor to reuse an
 * existing connection. Alternatively, a DSN can be passed to open a new one.
 *
 * Requirements:
 * - File "Liveuser.php" (contains the parent class "LiveUser")
 * - Array of connection options or a PEAR::MDB connection object must be
 *   passed to the constructor.
 *   Example: array('dsn' => 'mysql://user:pass@host/db_name')
 *              OR
 *            &$conn (PEAR::MDB connection object)
 *
 * @author  Lukas Smith <smith@backendmedia.com>
 * @author  Bjoern Kraus <krausbn@php.net>
 * @version $Id: MDB.php 137 2009-11-09 13:24:37Z vanpouckesven $
 * @package LiveUser
 * @category authentication
 */
class Tree_Storage_MDB extends Tree_Storage_SQL
{
    /**
     * Initializes database storage container.
     * Connects to database or uses existing database connection.
     *
     * @param array &$storageConf Storage Configuration
     * @return boolean false on failure and true on success
     *
     * @access public
     * @uses Tree_Storage_SQL::init
     */ 
    function init(&$storageConf)
    {
        if (isset($storageConf['connection']) &&
            MDB::isConnection($storageConf['connection'])
        ) {
            $this->dbc = &$storageConf['connection'];
        } elseif (isset($storageConf['dsn'])) {
            $this->dsn = $storageConf['dsn'];
            $function = null;
            if (isset($storageConf['function'])) {
                $function = $storageConf['function'];
            }
            $options = null;
            if (isset($storageConf['options'])) {
                $options = $storageConf['options'];
            }
            $options['optimize'] = 'portability';
            if ($function == 'singleton') {
                $this->dbc =& MDB::singleton($storageConf['dsn'], $options);
            } else {
                $this->dbc =& MDB::connect($storageConf['dsn'], $options);
            }
            if (PEAR::isError($this->dbc)) {
                return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null,  'could not create connection: '.$this->dbc->getMessage());
            }
        }
        return true;
    }

    /**
     * Convert a text value into a DBMS specific format that is suitable to
     * compose query statements.
     *
     * @param string $value text string value that is intended to be converted.
     * @param string $type type to which the value should be converted to
     * @return stringtext string that represents the given argument value in
     *       a DBMS specific format.
     *
     * @access public
     * @uses MDB::getValue
     */
    function quote($value, $type)
    {
        return $this->dbc->getValue($type, $value);
    }

    /**
     * Apply a type to all values of an array and return as a comma
     * seperated string useful for generating IN statements
     *
     * @param array $array data array
     * @param string $type determines type of the field
     *
     * @return string comma seperated values
     *
     * @access public
     * @uses MDB::getValue
     */
    function implodeArray($array, $type)
    {
        if (!is_array($array) || empty($array)) {
            return 'NULL';
        }
        foreach ($array as $value) {
            $return[] = $this->dbc->getValue($type, $value);
        }
        return implode(', ', $return);
    }

    /**
     *  Sets the range of the next query
     *
     * @param string $limit number of rows to select
     * @param string $offset first row to select
     *
     * @return
     *
     * @access public
     * @uses MDB::setSelectedRowRange
     */
    function setLimit($limit, $offset)
    {
        if ($limit || $offset) {
            return $this->dbc->setSelectedRowRange($offset, $limit);
        }
    }

    /**
     * Execute query
     *
     * @param string $query query
     * @return boolean | integer
     *
     * @access public
     * @uses MDB::query MDB::affectedRows
     */
    function query($query)
    {
        $result = $this->dbc->query($query);
        if (PEAR::isError($result)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $result->getMessage() . '-' . $result->getUserInfo());
        }
        return $this->dbc->affectedRows();
    }

    /**
     * Execute the specified query, fetch the value from the first column of
     * the first row of the result set and then frees
     * the result set.
     *
     * @param string $query the SELECT query statement to be executed.
     * @param string $type argument that specifies the expected
     *       datatype of the result set field, so that an eventual conversion
     *       may be performed. The default datatype is text, meaning that no
     *       conversion is performed
     * @return boolean | array
     *
     * @access public
     * @uses MDB::queryOne
     */
    function queryOne($query, $type)
    {
        if (is_array($type)) {
            $type = reset($type);
        }
        $result = $this->dbc->queryOne($query, $type);
        if (PEAR::isError($result)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $result->getMessage() . '-' . $result->getUserInfo());
        }
        return $result;
    }

    /**
     * Execute the specified query, fetch the values from the first
     * row of the result set into an array and then frees
     * the result set.
     *
     * @param string $query the SELECT query statement to be executed.
     * @param array $type array argument that specifies a list of
     *       expected datatypes of the result set columns, so that the eventual
     *       conversions may be performed. The default list of datatypes is
     *       empty, meaning that no conversion is performed.
     * @return boolean | array
     *
     * @access public
     * @uses MDB::queryRow
     */
    function queryRow($query, $type)
    {
        $result = $this->dbc->queryRow($query, $type, MDB_FETCHMODE_ASSOC);
        if (PEAR::isError($result)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $result->getMessage() . '-' . $result->getUserInfo());
        }
        return $result;
    }

    /**
     * Execute the specified query, fetch the value from the first column of
     * each row of the result set into an array and then frees the result set.
     *
     * @param string $query the SELECT query statement to be executed.
     * @param string $type argument that specifies the expected
     *       datatype of the result set field, so that an eventual conversion
     *       may be performed. The default datatype is text, meaning that no
     *       conversion is performed
     * @return boolean | array
     *
     * @access public
     * @uses MDB::queryCol
     */
    function queryCol($query, $type)
    {
        if (is_array($type)) {
            $type = reset($type);
        }
        $result = $this->dbc->queryCol($query, $type);
        if (PEAR::isError($result)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $result->getMessage() . '-' . $result->getUserInfo());
        }
        return $result;
    }

    /**
     * Execute the specified query, fetch all the rows of the result set into
     * a two dimensional array and then frees the result set.
     *
     * @param string $query the SELECT query statement to be executed.
     * @param array $types array argument that specifies a list of
     *       expected datatypes of the result set columns, so that the eventual
     *       conversions may be performed. The default list of datatypes is
     *       empty, meaning that no conversion is performed.
     * @param boolean $rekey if set to true, the $all will have the first
     *       column as its first dimension
     * @param boolean $group if set to true and $rekey is set to true, then
     *      all values with the same first column will be wrapped in an array
     * @param boolean $group if set to true and $rekey is set to true, then
     *      all values with the same first column will be wrapped in an array
     * @return boolean | array
     *
     * @access public
     * @uses MDB::queryAll
     */
    function queryAll($query, $types, $rekey, $group)
    {
        $result = $this->dbc->queryAll($query, $types, MDB_FETCHMODE_ASSOC, $rekey, false, $group);
        if (PEAR::isError($result)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $result->getMessage() . '-' . $result->getUserInfo());
        }
        return $result;
    }

    /**
     * returns the next free id of a sequence
     *
     * @param string $seqname name of the sequence
     * @param boolean $ondemand when true the seqence is
     *                           automatic created, if it not exists
     * @return boolean | integer
     *
     * @access public
     * @uses MDB::nextId
     */
    function nextId($seqname, $ondemand = true)
    {
        $result = $this->dbc->nextId($seqname, $ondemand);
        if (PEAR::isError($result)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $result->getMessage() . '-' . $result->getUserInfo());
        }
        return $result;
    }

    /**
     * returns the next free id of a sequence if the RDBMS
     * does not support auto increment
     *
     * @param string $table name of the table into which a new row was inserted
     * @param boolean $ondemand when true the seqence is
     *                          automatic created, if it not exists
     * @return boolean | integer
     *
     * @access public
     * @uses MDB::nextId
     */
    function getBeforeId($table, $ondemand = true)
    {
        $result = $this->dbc->nextId($table, $ondemand);
        if (PEAR::isError($result)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $result->getMessage() . '-' . $result->getUserInfo());
        }
        return $result;
    }

    /**
     * returns the autoincrement ID if supported or $id
     *
     * getAfterId isn't implemented in MDB so we return the $id that
     * was passed by the user
     *
     * @param string $id value as returned by getBeforeId()
     * @param string $table name of the table into which a new row was inserted
     * @return integer returns the id that the users passed via params
     *
     * @access public
     */
    function getAfterId($id, $table)
    {
        return $id;
    }

    /**
     *
     * @return mixed false on error or the result
     *
     * @access public
     * @uses MDB::disconnect
     */
    function disconnect()
    {
        $result = $this->dbc->disconnect();
        if (PEAR::isError($result)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $result->getMessage() . '-' . $result->getUserInfo());
        }
        return $result;
    }
}
?>
