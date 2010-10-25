<?php

// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2005 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Wolfram Kriesing <wolfram@kriesing.de>                      |
// |          Lorenzo Alberton <l.alberton@quipo.it>                      |
// |          Helgi Ãžormar ÃžorbjÃ¶rnsson <dufuz@php.net>              |
// +----------------------------------------------------------------------+

// $Id: SQLsimple.php 137 2009-11-09 13:24:37Z vanpouckesven $

require_once 'Tree/Memory.php';

/**
* the SQL interface to the tree class
*
* @access public
* @author Wolfram Kriesing <wolfram@kriesing.de>
* @author Helgi Þormar Þorbjörnsson <dufuz@php.net>
* @author Lorenzo Alberton <l.alberton@quipo.it>
* @package Tree
*/
class Tree_Memory_SQLsimple extends Tree_Memory
{
    // {{{ Tree_Memory_MDBsimple()

    /**
     * set up this object
     *
     * @access public
     * @param string $dsn this is a DSN of the for that PEAR::DB uses it
     *                    only that additionally you can add parameters like ...?table=test_table
     *                    to define the table it shall work on
     * @param array $options additional options you can set
     */
    function Tree_Memory_SQLsimple($config)
    {
        $this->_construct($config);
    }

    function __construct($config)
    {
        $this->conf = Tree::arrayMergeClobber($this->conf, $config['options']);
        $this->init($config);
    }

    // }}}
    // {{{ setup()

    /**
     * retrieve all the navigation data from the db and call build to build the
     * tree in the array data and structure
     *
     * @access public
     * @return boolean true on success
     */
    function setup()
    {
        // TODO sort by prevId (parent_id, prevId $addQuery) too if it exists in the table,
        //      or the root might be wrong, since the prevId of the root should be 0

        $where = $this->conf['whereAddOn']
                ? 'WHERE ' . $this->conf['whereAddOn'] : '';

        $order = $this->conf['order'] ? ',' . $this->conf['order'] : '';

        $order = $this->conf['fields']['parent_id']['name'] . $order;

        // build the query this way, that the root, which has no parent (parent_id=0)
        // and no previous (prevId=0) is in first place (in case prevId is given)
        $query = sprintf("SELECT * FROM %s %s ORDER BY %s",
                         $this->conf['table'],
                         $where,
                         $order); //,prevId !!!!
        $res = $this->_storage->queryAll($query, array(), false, false);
        if (PEAR::isError($res)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $res->getMessage());
        }
        // if the db-column names need to be mapped to different names
        // FIXXME somehow we should be able to do this in the query, but I dont know
        // how to select only those columns, use "as" on them and select the rest,
        //without getting those columns again :-(
        $res = $this->_prepareResults($res);

        return $res;
    }

    // }}}
    // {{{ add()

    /**
     * adds _one_ new element in the tree under the given parent
     * the values' keys given have to match the db-columns, because the
     * value gets inserted in the db directly
     * to add an entire node containing children and so on see 'addNode()'
     *
     * to be compatible with the MDBnested, u can also give the parent and previd
     * as the second and third parameter
     *
     * @see addNode
     * @access public
     * @param array $newValues this array contains the values that shall be inserted in the db-table
     *                         the key for each element is the name of the column
     * @return mixed either boolean false on failure or the id of the inserted row
     */
    function add($data, $parent_id = 0)
    {
        // FIXXME use $this->dbc->tableInfo to check which columns exist
        // so only data for which a column exist is inserted
        if ($parent_id) {
            $data['parent_id'] = $parent_id;
        }

        $newData = array();
        foreach ($data as $key => $value) {
            // quote the values, as needed for the insert
            $type = $this->conf['fields'][$key]['type'];
            $name = $this->_getColName($key);
            $newData[$name] = $this->_storage->quote($value, $type);
        }

        // use sequences to create a new id in the db-table
        $id = $this->_storage->nextId($this->conf['table']);
        if (PEAR::isError($id)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $id->getMessage() . '-' . $id->getUserInfo());
        }

        $query = sprintf("INSERT INTO %s (%s, %s) VALUES (%s, %s)",
                         $this->conf['table'] ,
                         $this->_getColName('id'),
                         implode(', ', array_keys($newData)) ,
                         $id,
                         implode(', ', $newData));
        $result = $this->_storage->query($query);
        if (PEAR::isError($result)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $result->getMessage() . '-' . $result->getUserInfo());
        }

        return $id;
    }

    // }}}
    // {{{ remove()

    /**
     * removes the given node
     *
     * @access public
     * @param  mixed $id the id of the node to be removed, or an array of id's to be removed
     * @return boolean true on success
     */
    function remove($id)
    {
        // if the one to remove has children, get their id's to remove them too
        if ($this->hasChildren($id)) {
            $id = $this->walk(array('_remove', $this), $id, 'array');
        } else {
            $id = array($id);
        }

        $where = "
            WHERE {$this->conf['fields']['id']['name']}
            IN (" . implode(', ', $id) . ')';

        $query = 'DELETE FROM ' . $this->conf['table'] . $where;
        $result = $this->_storage->query($query);
        if (PEAR::isError($result)) {
           return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $result->getMessage() . '-' . $result->getUserInfo());
        }

        // TODO if remove succeeded set prevId of the following element properly
        return true;
    }

    // }}}
    // {{{ move()

    /**
     * move an entry under a given parent or behind a given entry
     *
     * @version 2001/10/10
     * @access public
     * @author Wolfram Kriesing <wolfram@kriesing.de>
     * @param integer $idToMove the id of the element that shall be moved
     * @param integer $newparent_id the id of the element which will be the new parent
     * @param integer $newPrevId if prevId is given the element with the id idToMove
     *                                        shall be moved _behind_ the element with id=prevId
     *                                        if it is 0 it will be put at the beginning
     *                                        if no prevId is in the DB it can be 0 too and won't bother
     *                                        since it is not written in the DB anyway
     * @return boolean true for success
     */
    function move($idToMove, $newParent, $newPrevId = 0)
    {
        $idName     = $this->conf['fields']['id']['name'];
        $parentName = $this->conf['fields']['parent_id']['name'];

        // FIXXME todo: previous stuff
        // set the parent in the DB
        $query = "
            UPDATE {$this->conf['table']} SET
                $parentName = $newParent
            WHERE $idName = $idToMove";
        $result = $this->_storage->query($query);
        if (PEAR::isError($result)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $result->getMessage() . '-' . $result->getUserInfo());
        }
        // FIXXME update the prevId's of the elements where the element was moved away from and moved in
        return true;
    }

    // }}}
    // {{{ update()

    /**
     * update an element in the DB
     *
     * @version 2002/01/17
     * @access public
     * @author Wolfram Kriesing <wolfram@kriesing.de>
     * @param array $newData all the new data, the key 'id' is used to
                             build the 'WHERE id=' clause and all the other
     *                       elements are the data to fill in the DB
     * @return boolean true for success
     */
    function update($id, $data)
    {
        // FIXXME check $this->dbc->tableInfo to see if all the columns that shall be updated
        // really exist, this will also extract nextId etc. if given before writing it in the DB
        // in case they dont exist in the DB
        $setData = array();
        foreach ($data as $key => $value) { // quote the values, as needed for the insert
            $type = $this->conf['fields'][$key]['type'];
            $setData[] = $this->_getColName($key) . ' = ' . $this->_storage->quote($value, $type);
        }

        $query = sprintf('UPDATE %s SET %s WHERE %s = %s',
                         $this->conf['table'],
                         implode(',', $setData),
                         $this->_getColName('id'),
                         $id
                );
        $result = $this->_storage->query($query);
        if (PEAR::isError($result)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $result->getMessage() . '-' . $result->getUserInfo());
        }

        return true;
    }

    // }}}
}
?>