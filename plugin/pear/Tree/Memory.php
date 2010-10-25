<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
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
// +----------------------------------------------------------------------+
//
//  $Id: Memory.php 137 2009-11-09 13:24:37Z vanpouckesven $

require_once 'Tree/Tree.php';

/**
 * this class can be used to step through a tree using ['parent'],['child']
 * the tree is saved as flat data in a db, where at least the parent
 * needs to be given if a previous member is given too then the order
 * on a level can be determined too
 * actually this class was used for a navigation tree
 * now it is extended to serve any kind of tree
 * you can unambigiously refer to any element by using the following
 * syntax
 * tree->data[currentId][<where>]...[<where>]
 * <where> can be either "parent", "child", "next" or "previous", this way
 * you can "walk" from any point to any other point in the tree
 * by using <where> in any order you want
 * example (in parentheses the id):
 * root
 *  +---level 1_1 (1)
 *  |      +----level 2_1 (2)
 *  |      +----level 2_2 (3)
 *  |              +-----level 3_1 (4)
 *  +---level 1_2 (5)
 *
 *  the database table to this structure (without defined order)
 *  id     parent_id        name
 *  1         0         level 1_1
 *  2         1         level 2_1
 *  3         1         level 2_1
 *  4         3         level 3_1
 *  5         0         level 1_2
 *
 * now you can refer to elements for example like this (all examples assume you
 * know the structure):
 * go from "level 3_1" to "level 1_1": $tree->data[4]['parent']['parent']
 * go from "level 3_1" to "level 1_2":
 *          $tree->data[4]['parent']['parent']['next']
 * go from "level 2_1" to "level 3_1": $tree->data[2]['next']['child']
 * go from "level 2_2" to "level 2_1": $tree->data[3]['previous']
 * go from "level 1_2" to "level 3_1":
 *          $tree->data[5]['previous']['child']['next']['child']
 *
 * on a pentium 1.9 GHz 512 MB RAM, Linux 2.4, Apache 1.3.19, PHP 4.0.6
 * performance statistics for version 1.26, using examples/Tree/Tree.php
 *  -   reading from DB and preparing took: 0.14958894252777
 *  -   building took: 0.074488043785095
 *  -  buildStructure took: 0.05151903629303
 *  -  setting up the tree time: 0.29579293727875
 *  -  number of elements: 1564
 *  -  deepest level: 17
 * so you can use it for tiny-big trees too :-)
 * but watch the db traffic, which might be considerable, depending
 * on your setup.
 *
 * FIXXXME there is one really bad thing about the entire class, at some points
 * there are references to $this->data returned, or the programmer can even
 * access this->data, which means he can change the structure, since this->data
 * can not be set to read-only, therefore this->data has to be handled
 * with great care!!! never do something like this:
 * <code>
 * $x = &$tree->data[<some-id>]; $x = $y;
 * </code>
 * this overwrites the element in the structure !!!
 *
 *
 * @access   public
 * @author   Wolfram Kriesing <wolfram@kriesing.de>
 * @version  2001/06/27
 * @package  Tree
 */
class Tree_Memory extends Tree
{
    /**
     * this array contains the pure data from the DB
     * which are always kept, since all other structures will
     * only make references on any element
     * and those data are extended by the elements 'parent' 'children' etc...
     * @var    array $data
     */
    var $data = array();

    /**
     * this array contains references to this->data but it
     * additionally represents the directory structure
     * that means the array has as many dimensions as the
     * tree structure has levels
     * but this array is only used internally from outside you can do
     * everything using the node-id's
     *
     * @var    array $structure
     * @access private
     */
    var $structure = array();

    /**
     * it contains all the parents and their children, where the parent_id is the
     * key and all the children are the values, this is for speeding up
     * the tree-building process
     *
     * @var    array   $children
     */
    var $children = array();

    /**
     * @see &getNode()
     * @see &_getNode()
     * @access private
     * @var integer variable only used in the method getNode and _getNode
     */
    var $_getNodeMaxLevel;

    /**
     * @see    &getNode()
     * @see    &_getNode()
     * @access private
     * @var    integer  variable only used in the method getNode and
     *                  _getNode
     */
    var $_getNodeCurParent;

    /**
     * the maximum depth of the tree
     * @access private
     * @var    int     the maximum depth of the tree
     */
    var $_treeDepth = 0;

    var $walkReturn = array();

    // {{{ Tree_Memory()

    /**
     * set up this object
     *
     * @version   2001/06/27
     * @access    public
     * @author    Wolfram Kriesing <wolfram@kriesing.de>
     * @param mixed   this is a DSN for the PEAR::DB, can be
     *                            either an object/string
     * @param array   additional options you can set
     */
    function Tree_Memory($config)
    {
        $this->__construct($config);
    }

    function __construct($config)
    {
        $type = strtolower($config['type']);
        $this->conf = Tree::arrayMergeClobber($this->conf, $config['options']);
        if (
            ($type == 'simple' || $type == 'nested')
            && in_array(strtoupper($config['storage']['name']), array('DB', 'MDB', 'MDB2'))
        ) {
            $name = 'SQL';
        } else {
            $name = strtoupper($config['storage']['name']);
        }

        include_once 'Tree/Memory/' . $name . $type . '.php';
        $className = 'Tree_Memory_' . $name . $type;

        $this->dataClass =& new $className($config);
    }

    // }}}


    // {{{ switchDataSource()

    /**
     * use this to switch data sources on the run
     * i.e. if you are reading the data from a db-tree and want to save it
     * as xml data (which will work one day too)
     * or reading the data from an xml file and writing it in the db
     * which should already work
     *
     * @version 2002/01/17
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   string  this is a DSN of the for that PEAR::DB uses it
     *                  only that additionally you can add parameters
     *                  like ...?table=test_table to define the table.
     * @param   array   additional options you can set
     * @return  boolean true on success
     */
    function switchDataSource($config)
    {
        $data = $this->getNode();
        //$this->Tree($dsn, $options);
        $this->Tree_Memory($config);

        // this method prepares data retreived using getNode to be used
        // in this type of tree
        $this->dataClass->setData($data);
        $this->setup();
    }

    // }}}
    // {{{ setupByRawData()

    /**
     *
     *
     * @version 2002/01/19
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @return  boolean true if the setup succeeded
     * @
     */
    function setupByRawData($string)
    {
        // expects
        // for XML an XML-String,
        // for DB-a result set, may be or an array, dont know here
        // not implemented yet
        $res = $this->dataClass->setupByRawData($string);
        return $this->_setup($res);
    }

    // }}}
    // {{{ setup()

    /**
     * @version 2002/01/19
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   array   the result of a query which retreives (all)
     *                  the tree data from a source
     * @return  true or Tree_Error
     */
    function setup($data = null)
    {
        $res = $this->dataClass->setup($data);
        if (PEAR::isError($res)) {
            return $res;
        }

        return $this->_setup($res);
    }

    // }}}
    // {{{ _setup()

    /**
     * retreive all the navigation data from the db and build the
     * tree in the array data and structure
     *
     * @version 2001/11/20
     * @access  private
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @return  boolean     true on success
     */
    function _setup($setupData)
    {
        // TODO sort by prevId (parent_id,prevId $addQuery) too if it exists
        // in the table, or the root might be wrong TODO since the prevId
        // of the root should be 0
        if (!$setupData) {
            return false;
        }

        //FIXXXXXME validate the structure.
        // i.e. a problem occurs, if you give one node, which has a parent_id=1,
        // it screws up everything!!!
        //empty the data structures, since we are reading the data
        // from the db (again)
        $this->structure = $this->data = $this->children = array();

        // build an array where all the parents have their children as a member
        // this i do to speed up the buildStructure
        $copy = $setupData;
        foreach ($setupData as $key => $values) {
            if (is_array($values)) {
                $this->data[$values['id']] = $values;
                $this->children[$values['parent_id']][] = $values['id'];
                $this->structure[$values['id']] = $values['parent_id'];

                if ($values['parent_id'] == '0') {
                    $this->data[$values['id']]['level'] = 0;
                    unset($copy[$key]);
                }
            } else {
                unset($copy[$key]);
            }
        }
        unset($setupData);

        // Get the levels for each item.
        do {
            foreach ($copy as $key => $node) {
                if (!array_key_exists('parent_id', $node)) {
                    unset($copy[$key]);
                    continue;
                }

                if (isset($this->data[$node['parent_id']]['level'])) {
                    $level = $this->data[$node['parent_id']]['level'] + 1;
                    $this->data[$node['id']]['level'] = $level;

                    if ($level > $this->_treeDepth) {
                        $this->_treeDepth = $level;
                    }
                    unset($copy[$key]);
                }
            }
        } while (count($copy));
        unset($copy);

        // walk through all the children on each level and set the
        // next/previous relations of those children, since all children
        // for "children[$id]" are on the same level we can do
        // this here :-)
        foreach ($this->children as $children) {
            $lastPrevId = 0;
            if (count($children)) {
                foreach ($children as $key) {
                    if ($lastPrevId) {
                        // remember the nextId too, so the build process can
                        // be speed up
                        $this->data[$lastPrevId]['nextId'] = $key;

                        $this->data[$key]['prevId'] = $lastPrevId;
                    }
                    $lastPrevId = $key;
                }
            }
        }

        return true;
    }

    // }}}
    // {{{ add()

    /**
     * adds _one_ new element in the tree under the given parent
     * the values' keys given have to match the db-columns, because the
     * value gets inserted in the db directly
     * to add an entire node containing children and so on see 'addNode()'
     * @see addNode()
     * @version 2001/10/09
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   array   this array contains the values that shall be inserted
     *                  in the db-table
     * @param   int the parent id
     * @param   int the prevId
     * @return  mixed   either boolean false on failure or the id
     *                  of the inserted row
     */
    function add($data, $parent_id = 0, $prevId = 0)
    {
        // see comments in 'move' and 'remove'
        if (method_exists($this->dataClass, 'add')) {
            $result = $this->dataClass->add($data, $parent_id, $prevId);
            /*if (!PEAR::isError($result)) {
                // Setup parent/child relationship
                $this->children[$parent_id][] = $nextId;

                // Add to list of nodes
                $this->structure[$result] = $parent_id;

                // Add data
               $this->data[$result] = $newValues;
            }*/
            return $result;
        }
        return Tree::raiseError('TREE_ERROR_NOT_IMPLEMENTED');
    }

    // }}}
    // {{{ remove()

    /**
     * removes the given node and all children if removeRecursively is on
     *
     * @version    2002/01/24
     * @access     public
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      mixed   $id     the id of the node to be removed
     * @return     boolean true on success
     */
    function remove($id)
    {
        // see comment in 'move'
        // if the prevId is in use we need to update the prevId of the element
        // after the one that is removed too, to have the prevId of the one
        // that is removed!!!
        if (method_exists($this->dataClass, 'remove')) {
            // Remove child nodes first
            if (isset($this->children[$id])) {
                foreach ($this->children[$id] as $child) {
                    $this->remove($child);
                }
            }

            $result =  $this->dataClass->remove($id);
            /*if ($result) {
                // Remove childIDs data
                if (isset($this->children[$id])) {
                    unset($this->children[$id]);
                }

                // Remove data
                if (isset($this->data[$id])) {
                    unset($this->data[$id]);
                }

                // Remove from structure array
                if (isset($this->structure[$id])) {
                    unset($this->structure[$id]);
                }
            }*/
            return $result;
        }

        return Tree::raiseError('TREE_ERROR_NOT_IMPLEMENTED');
    }

    // }}}
    // {{{ _remove()

    /**
     * collects the ID's of the elements to be removed
     *
     * @version    2001/10/09
     * @access     public
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      mixed   $id   the id of the node to be removed
     * @return     boolean true on success
     */
    function _remove($element)
    {
        return $element['id'];
    }

    // }}}
    // {{{ update()

    /**
     * update data in a node
     *
     * @version    2002/01/29
     * @access     public
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      integer the ID of the element that shall be updated
     * @param      array   the data to update
     * @return     mixed   either boolean or
     *                     an error object if the method is not implemented
     */
    function update($id, $data)
    {
        if (method_exists($this->dataClass, 'update')) {
            $result = $this->dataClass->update($id, $data);
            /*if ($result) {
                // Setup parent/child relationship - Needs to be done a check here to see if it needs unset/add
                $this->children[$data['parent_id'][] = $id;

                // Add to list of nodes
                $this->structure[$id] = $data['parent_id'];

                // Update data
               $this->data[$id] = $data;
            }*/
            return $result;
        }
        return Tree::raiseError('TREE_ERROR_NOT_IMPLEMENTED');
    }

    // }}}
    // {{{ move()

    /**
     * move an entry under a given parent or behind a given entry.
     * !!! the 'move behind another element' is only implemented for nested
     * trees now!!!.
     * If a newPrevId is given the newparent_id is dismissed!
     * call it either like this:
     *      $tree->move(x, y)
     *      to move the element (or entire tree) with the id x
     *      under the element with the id y
     * or
     * <code>
     *      // ommit the second parameter by setting it to 0
     *      $tree->move(x, 0, y);
     * </code>
     *      to move the element (or entire tree) with the id x
     *      behind the element with the id y
     * or
     * <code>
     *      $tree->move(array(x1,x2,x3), ...
     * </code>
     *      the first parameter can also be an array of elements that shall
     *      be moved
     *      the second and third para can be as described above
     *
     * @version 2002/06/08
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   integer the id(s) of the element(s) that shall be moved
     * @param   integer the id of the element which will be the new parent
     * @param   integer if prevId is given the element with the id idToMove
     *                  shall be moved _behind_ the element
     *                  with id=prevId if it is 0 it will be put at
     *                  the beginning
     * @return     boolean     true for success
     */
    function move($idsToMove, $newparent_id, $newPrevId = 0)
    {
        $errors = array();
        foreach ((array)$idsToMove as $idToMove) {
            $ret = $this->_move($idToMove, $newparent_id, $newPrevId);
            if (PEAR::isError($ret)) {
                $errors[] = $ret;
            }
        }
// FIXXME return a Tree_Error, not an array !!!!!
        if (count($errors)) {
            return $errors;
        }
        return true;
    }

    // }}}
    // {{{ _move()

    /**
     * this method moves one tree element
     *
     * @see move()
     * @version 2001/10/10
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   integer     the id of the element that shall be moved
     * @param   integer the id of the element which will be
     *                  the new parent
     * @param   integer if prevId is given the element with the id idToMove
     *                  shall be moved _behind_ the element with id=prevId
     *                  if it is 0 it will be put at the beginning
     * @return  mixed   true for success, Tree_Error on failure
     */
    function _move($idToMove, $newparent_id, $prevId = 0)
    {
        // itself can not be a parent of itself
        if ($idToMove == $newparent_id) {
            return Tree::raiseError('TREE_ERROR_INVALID_PARENT');
        }

        // check if $newparent_id is a child (or a child-child ...) of $idToMove
        // if so prevent moving, because that is not possible
        // does this element have children?
        if ($this->hasChildren($idToMove)) {
            $allChildren = $this->getChildren($idToMove);
            // FIXXME what happens here we are changing $allChildren,
            // doesnt this change the property data too??? since getChildren
            // (might, not yet) return a reference use while since foreach
            // only works on a copy of the data to loop through, but we are
            // changing $allChildren in the loop
            while (list(, $aChild) = each($allChildren)) {
                // remove the first element because if array_merge is called
                // the array pointer seems to be
                array_shift($allChildren);
                // set to the beginning and this way the beginning is always
                // the current element, simply work off and truncate in front
                if (@$aChild['children']) {
                    $allChildren =
                        array_merge($allChildren, $aChild['children']);
                }
                if ($newparent_id == $aChild['id']) {
                    return Tree::raiseError('TREE_ERROR_INVALID_PARENT');
                }
            }
        }

        // what happens if i am using the prevId too, then the db class also
        // needs to know where the element should be moved to
        // and it has to change the prevId of the element that will be after it
        // so we may be simply call some method like 'update' too?
        if (method_exists($this->dataClass, 'move')) {
            return $this->dataClass->move($idToMove,
                                                $newparent_id,
                                                $prevId
                                            );
        }
        return Tree::raiseError('TREE_ERROR_NOT_IMPLEMENTED');
    }

    // }}}

    //
    //
    //  from here all methods are not interacting on the  'dataClass'
    //
    //

    // {{{ walk()

    /**
     * this method only serves to call the _walk method and
     * reset $this->walkReturn that will be returned by all the walk-steps
     *
     * @version 2001/11/25
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   mixed   the name of the function to call for each walk step,
     *                  or an array for a method, where [0] is the method name
     *                  and [1] the object
     * @param   array   the id to start walking through the tree, everything
     *                  below is walked through
     * @param   string  the return of all the walk data will be of the given
     *                  type (values: string, array)
     * @return  mixed   this is all the return data collected from all
     *                  the walk-steps
     */
    function walk($walkFunction, $id = 0, $returnType = 'string')
    {
        // by default all of structure is used
        if ($id === 0) {
            $useNode = $this->structure;
            $keys = array_keys($this->structure);
            $id = $keys[0];
        } else {
            // get the path, to be able to go to the element in this->structure
            $path = $this->getPath($id);
            if (empty($path)) {
                return array();
            }

            // pop off the last element, since it is the one requested
            array_pop($path);

            // start at the root of structure
            $curNode = $this->structure;
            foreach ($path as $node) {
                // go as deep into structure as path defines
                $curNode = $curNode[$node['id']];
            }
            // empty it first, so we dont have the other stuff in there
            // from before
            $useNode = array();
            // copy only the branch of the tree that the parameter
            // $id requested
            $useNode[$id] = $curNode[$id];
        }

        // a new walk starts, unset the return value
        unset($this->walkReturn);
        return $this->_walk($walkFunction, $useNode, $returnType);
    }

    // }}}
    // {{{ _walk()

    /**
     * walks through the entire tree and returns the current element and the level
     * so a user can use this to build a treemap or whatever
     *
     * @version 2001/06/xx
     * @access  private
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   mixed   the name of the function to call for each walk step,
     *                  or an array for a method, where [0] is the method name
     *                  and [1] the object
     *
     * @param   array   the reference in the this->structure, to walk
     *                  everything below
     * @param   string  the return of all the walk data will be
     *                  of the given type (values: string, array, ifArray)
     * @return  mixed   this is all the return data collected from all
     *                  the walk-steps
     */
    function _walk($walkFunction, &$curLevel, $returnType)
    {
        if (isset($curLevel) && is_array($curLevel)) {
            foreach ($curLevel as $key => $value) {
                $ret = call_user_func($walkFunction, $this->data[$key]);

                switch ($returnType) {
                    case 'array':
                        $this->walkReturn[] = $ret;
                        break;
                    // this only adds the element if the $ret is an array
                    // and contains data
                    case 'ifArray':
                        if (is_array($ret)) {
                            $this->walkReturn[] = $ret;
                        }
                        break;
                    default:
                        $this->walkReturn .= $ret;
                        break;
                }
                $this->_walk($walkFunction, $value, $returnType);
            }
        }

        return $this->walkReturn;
    }

    // }}}
    // {{{ addNode()

    /**
     * Adds multiple elements. You have to pass those elements
     * in a multidimensional array which represents the tree structure
     * as it shall be added (this array can of course also simply contain
     * one element).
     * The following array $x passed as the parameter
     *      $x[0] = array('name'     => 'bla',
     *                    'parent_id' => '30',
     *                    array('name'    => 'bla1',
     *                          'comment' => 'foo',
     *                          array('name' => 'bla2'),
     *                          array('name' => 'bla2_1')
     *                          ),
     *                    array('name'=>'bla1_1'),
     *                    );
     *      $x[1] = array('name'     => 'fooBla',
     *                    'parent_id' => '30');
     *
     * would add the following tree (or subtree/node) under the parent
     * with the id 30 (since 'parent_id'=30 in $x[0] and in $x[1]):
     *  +--bla
     *  |   +--bla1
     *  |   |    +--bla2
     *  |   |    +--bla2_1
     *  |   +--bla1_1
     *  +--fooBla
     *
     * @see add()
     * @version 2001/12/19
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   array   the tree to be inserted, represents the tree structure,
     *                             see add() for the exact member of each node
     * @return  mixed   either boolean false on failure
     *                  or the id of the inserted row
     */
    function addNode($node)
    {
        if (count($node)) {
            foreach ($node as $aNode) {
                $newNode = array();
                // this should always have data, if not the passed
                // structure has an error
                foreach ($aNode as $name => $value) {
                    // collect the data that need to go in the DB
                    if (!is_array($value)) {
                        $newEntry[$name] = $value;
                    } else {
                        // collect the children
                        $newNode[] = $value;
                    }
                }
                // add the element and get the id, that it got, to have
                // the parent_id for the children
                $insertedId = $this->add($newEntry);
                // if inserting suceeded, we have received the id
                // under which we can insert the children
                if ($insertedId != false) {
                    // if there are children, set their parent_id.
                    // So they kknow where they belong in the tree
                    if (count($newNode)) {
                        foreach($newNode as $key => $aNewNode) {
                            $newNode[$key]['parent_id'] = $insertedId;
                        }
                    }
                    // call yourself recursively to insert the children
                    // and its children
                    $this->addNode($newNode);
                }
            }
        }
    }

    // }}}
    // {{{ getPath()

    /**
     * gets the path to the element given by its id
     * !!! ATTENTION watch out that you never change any of the data returned,
     * since they are references to the internal property $data
     *
     * @access  public
     * @version 2001/10/10
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   mixed   the id of the node to get the path for
     * @return  array   this array contains all elements from the root
     *                      to the element given by the id
     *
     */
    function getPath($id)
    {
        if (!(int)$id) {
            return array();
        }

        // empty the path, to be clean
        $path = array();

        while (@$this->data[$id]['parent_id']) {
            // curElement is already a reference, so save it in path
            $path[] = &$this->data[$id];
            // get the next parent id, for the while to retreive the parent's parent
            $id = $this->data[$id]['parent_id'];
        }
        // dont forget the last one
        $path[] = &$this->data[$id];

        return array_reverse($path);
    }

    // }}}
    // {{{ _getElement()

    /**
     *
     *
     * @version    2002/01/21
     * @access     private
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      int     the element ID
     *
     */
    function &_getElement($id, $what = '')
    {
        // We should not return false, since that might be a value of the
        // element that is requested.
        $element = null;

        if ($what == '' && isset($this->data[$id])) {
            $element = &$this->_prepareResult($this->data[$id]);
        }

        if (isset($this->data[$id][$what])) {
            $element = &$this->_prepareResult($this->data[$this->data[$id][$what]]);
        }

        return $element;;
    }

    // }}}
    // {{{ getElement()

    /**
     * gets an element as a reference
     *
     * @version 2002/01/21
     * @access  private
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   int the element ID
     *
     */
    function &getElement($id)
    {
        $element = &$this->_getElement($id);
        return $element;
    }

    // }}}
    // {{{ getElementContent()

    /**
     *
     *
     * @version 2002/02/06
     * @access  private
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   mixed    either the id of an element
     *                      or the path to the element
     * @param string the field name
     *
     */
    function getElementContent($idOrPath, $fieldName)
    {
        if (is_string($idOrPath)) {
            $idOrPath = $this->getIdByPath($idOrPath);
        }
        return $this->data[$idOrPath][$fieldName];
    }

    // }}}
    // {{{ getElementsContent()

    /**
     *
     *
     * @version    2002/02/06
     * @access     private
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      int     the element ID
     * @param    string the field name
     *
     */
    function getElementsContent($ids, $fieldName) {
        // i dont know if this method is not just overloading the file.
        // Since it only serves my lazyness
        // is this effective here? i can also loop in the calling code!?
        $fields = array();
        if (isset($ids) && is_array($ids) && count($ids)) {
            foreach ($ids as $aId) {
                $fields[] = $this->getElementContent($aId, $fieldName);
            }
        }
        return $fields;
    }

    // }}}
    // {{{ getElementByPath()

    /**
     * gets an element given by it's path as a reference
     *
     * @version 2002/01/21
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   string  the path to search for
     * @param   integer the id where to search for the path
     * @param   string  the name of the key that contains the node name
     * @param   string  the path separator
     * @return  integer the id of the searched element
     *
     */
    function &getElementByPath($path, $startId = 0, $nodeName = 'name', $seperator = '/')
    {
        // null since false might be interpreted as id 0
        $element = null;

        $id = $this->getIdByPath($path, $startId);
        if ($id) {
            $element = &$this->_getElement($id);
        }

        return $element;
    }

    // }}}
    // {{{ getLevel()

    /**
     * get the level, which is how far below the root are we?
     *
     * @version 2001/11/25
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   mixed   $id     the id of the node to get the level for
     *
     */
    function getLevel($id)
    {
        return $this->data[$id]['level'];
    }

    // }}}
    // {{{ getParent()

    /**
     * returns the child if the node given has one
     * !!! ATTENTION watch out that you never change any of the data returned,
     * since they are references to the internal property $data
     *
     * @version 2001/11/27
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   mixed   $id     the id of the node to get the child for
     *
     */
    function &getParent($id)
    {
        $element = &$this->_getElement($id, 'parent_id');
        return $element;
    }

    // }}}
    // {{{ nextSibling()

    /**
     * returns the next element if the node given has one
     * !!! ATTENTION watch out that you never change any of the data returned,
     * since they are references to the internal property $data
     *
     * @version 2002/01/17
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   mixed   the id of the node to get the child for
     * @return  mixed   reference to the next element or false if there is none
     */
    function &nextSibling($id)
    {
        $element = &$this->_getElement($id, 'nextId');
        return $element;
    }

    // }}}
    // {{{ prevSibling()

    /**
     * returns the previous element if the node given has one
     * !!! ATTENTION watch out that you never change any of the data returned,
     * since they are references to the internal property $data
     *
     * @version 2002/02/05
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   mixed   the id of the node to get the child for
     * @return  mixed   reference to the next element or false if there is none
     */
    function &prevSibling($id)
    {
        $element = &$this->_getElement($id, 'prevId');
        return $element;
    }

    // }}}
    // {{{ getNode()

    /**
     * returns the node for the given id
     * !!! ATTENTION watch out that you never change any of the data returned,
     * since they are references to the internal property $data
     *
     * @version    2001/11/28
     * @access     public
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      mixed   $id     the id of the node to get
     *
     */
/*
    function &getNode($id)
    {
        $element = &$this->_getElement($id);
        return $element;
    }
*/

    // }}}
    // {{{ getIdByPath()

    /**
     * return the id of the element which is referenced by $path
     * this is useful for xml-structures, like: getIdByPath('/root/sub1/sub2')
     * this requires the structure to use each name uniquely
     * if this is not given it will return the first proper path found
     * i.e. there should only be one path /x/y/z
     *
     * @version    2001/11/28
     * @access     public
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      string  $path       the path to search for
     * @param      integer $startId    the id where to search for the path
     * @param      string  $nodeName   the name of the key that contains the node name
     * @param      string  $seperator  the path seperator
     * @return     integer the id of the searched element
     *
     */
    function getIdByPath($path, $startId = 0, $nodeName = 'name', $seperator = '/')
// should this method be called getElementIdByPath ????
    {
        // if no start ID is given get the root
        if ($startId == 0) {
            $root = $this->getFirstRoot();
            $startId = $root['id'];
        } else {   // if a start id is given, get its first child to start searching there
            $startId = $this->children[$startId][0];
            if ($startId === false) {            // is there a child to this element?
                return false;
            }
        }

        // if a seperator is at the beginning strip it off
        if (strpos($path, $seperator) === 0) {
            $path = substr($path, strlen($seperator));
        }

        $nodes = explode($seperator, $path);
        $nodeCount = count($nodes);
        $curId = $startId;

        foreach ($nodes as $key => $aNodeName) {
            $nodeFound = false;
            do {
                if (isset($this->data[$curId][$nodeName]) &&
                    $this->data[$curId][$nodeName] == $aNodeName
                ) {
                    $nodeFound = true;
                    // do only save the child if we are not already at the end of path
                    // because then we need curId to return it
                    if ($key < ($nodeCount - 1)) {
                        $curId = $this->children[$curId][0];
                    }
                    break;
                }

                $next = $this->nextSibling($curId);
                $curId = $next['id'];
            } while($curId);

            if ($nodeFound === false) {
                return false;
            }
        }

        return $curId;
        // FIXXME to be implemented
    }

    // }}}
    // {{{ getFirstRoot()

    /**
     * this gets the first element that is in the root node
     * i think that there can't be a "getRoot" method since there might
     * be multiple number of elements in the root node, at least the
     * way it works now
     *
     * @access     public
     * @version    2001/12/10
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @return     returns the first root element
     */
    function &getFirstRoot()
    {
        // could also be reset($this->data) i think since php keeps the order
        // ... but i didnt try
        reset($this->structure);
        return $this->data[key($this->structure)];
    }

    // }}}
    // {{{ getRoot()

    /**
     * since in a nested tree there can only be one root
     * which i think (now) is correct, we also need an alias for this method
     * this also makes all the methods in Tree, which access the
     * root element work properly!
     *
     * @access     public
     * @version    2002/07/26
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @return     returns the first root element
     */
    function &getRoot()
    {
        $element = &$this->getFirstRoot();
        return $element;
    }

    // }}}
    // {{{ getBranch()

    /**
     * gets the tree under the given element in one array, sorted
     * so you can go through the elements from begin to end and list them
     * as they are in the tree, where every child (until the deepest) is retreived
     *
     * @see        &_getBranch()
     * @access     public
     * @version    2001/12/17
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      integer  the id where to start walking
     * @param      integer  this number says how deep into
     *                      the structure the elements shall be
     *                      retreived
     * @return     array    sorted as listed in the tree
     */
    function &getBranch($startId = 0, $depth = 0)
    {
        $level = $startId == 0 ? 0 : $this->getLevel($startId);

        $this->_getNodeMaxLevel = $depth ? ($depth + $level) : 0 ;
        //!!!        $this->_getNodeCurParent = $this->data['parent']['id'];

        // if the tree is empty dont walk through it
        if (!count($this->data)) {
            $a = array();
            return $a;
        }

        $ret = $this->walk(array(&$this, '_getBranch'), $startId, 'ifArray');
        return $ret;
    }

    // }}}
    // {{{ _getNode()

    /**
     * this is used for walking through the tree structure
     * until a given level, this method should only be used by getNode
     *
     * @see        &getBranch()
     * @see        walk()
     * @see        _walk()
     * @access     private
     * @version    2001/12/17
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      array    the node passed by _walk
     * @return     mixed    either returns the node, or nothing
     *                      if the level _getNodeMaxLevel is reached
     */
    function &_getBranch(&$node)
    {
        if ($this->_getNodeMaxLevel) {
            if ($this->getLevel($node['id']) < $this->_getNodeMaxLevel) {
                return $node;
            }
            return;
        }

        return $node;
    }

    // }}}
    // {{{ getChildren()

    /**
     * returns the children of the given ids
     *
     * @version 2001/12/17
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   integer|array $id the id of the node to check for children
     * @param   boolean if only the first child should be returned (only used when one id is passed)
     * @param   integer the children of how many levels shall be returned

     * @return  boolean true if the node has children
     */
    function getChildren($ids, $oneChild = false, $levels = 1)
    {
        //FIXXME $levels to be implemented
        $ret = array();
        if (is_array($ids)) {
            foreach ($ids as $aId) {
                if ($this->hasChildren($aId)) {
                    foreach ($this->children[$aId] as $value) {
                        $ret[$aId][] = $this->data[$value];
                    }
                }
            }
        } else {
            if ($this->hasChildren($ids)) {
                foreach ($this->children[$ids] as $value) {
                    $ret[] = $this->data[$value];
                    if ($oneChild) {
                        return $ret = $ret[0];
                    }
                }
            }
        }
        return $ret;
    }

    // }}}
    // {{{ isNode()

    /**
     * returns if the given element is a valid node
     *
     * @version 2001/12/21
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   integer $id the id of the node to check for children
     * @return  boolean true if the node has children
     */
    function isNode($id = 0)
    {
        return isset($this->data[$id]);
    }

    // }}}

    /**
     * returns if the given element has any children
     *
     * @version 2001/12/17
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   integer $id the id of the node to check for children
     * @return  boolean true if the node has children
     */
    function hasChildren($id = 0)
    {
        if (isset($this->children[$id])) {
            return true;
        }
        return false;
    }

    // {{{ varDump()

    /**
     * this is for debugging, dumps the entire data-array
     * an extra method is needed, since this array contains recursive
     * elements which make a normal print_f or var_dump not show all the data
     *
     * @version 2002/01/21
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @params  mixed   either the id of the node to dump, this will dump
     *                  everything below the given node or an array of nodes
     *                  to dump. This only dumps the elements passed
     *                  as an array. 0 or no parameter if the entire tree shall
     *                  be dumped if you want to dump only a single element
     *                  pass it as an array using array($element).
     */
    function varDump($node = 0)
    {
        $dontDump = array('parent', 'child', 'children', 'next', 'previous');

        // if $node is an array, we assume it is a collection of elements
        if (!is_array($node)) {
            $nodes = $this->getBranch($node);
        } else {
            $nodes = $node;
        }
        // if $node==0 then the entire tree is retreived
        if (count($node)) {
            echo '<table border="1"><tr><th>name</th>';
            $keys = array();
            foreach ($this->getRoot() as $key => $x) {
                if (!is_array($x)) {
                    echo "<th>$key</th>";
                    $keys[] = $key;
                }
            }
            echo '</tr>';

            foreach ($nodes as $aNode) {
                echo '<tr><td nowrap="nowrap">';
                $prefix = '';
                for ($i = 0; $i < $aNode['level']; $i++) $prefix .= '- ';
                echo "$prefix {$aNode['name']}</td>";
                foreach ($keys as $aKey) {
                    if (!is_array($key)) {
                        $val = isset($aNode[$aKey]) ? $aNode[$aKey] : '&nbsp;';
                        echo "<td>$val</td>";
                    }
                }
                echo '</tr>';
            }
            echo '</table>';
        }
    }

    // }}}

    //### TODO's ###

    // {{{ copy()
    /**
     * NOT IMPLEMENTED YET
     * copies a part of the tree under a given parent
     *
     * @version 2001/12/19
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   the id of the element which is copied, all its children are copied too
     * @param   the id that shall be the new parent
     * @return  boolean     true on success
     */
    function copy($srcId, $destId)
    {
        if (method_exists($this->dataClass, 'copy')) {
            return $this->dataClass->copy($srcId, $destId);
        }
        return Tree::raiseError('TREE_ERROR_NOT_IMPLEMENTED');
/*
    remove all array elements after 'parent' since those had been created
    and remove id and set parent_id and that should be it, build the tree and pass it to addNode

    those are the fields in one data-entry
id=>41
parent_id=>39
name=>Java
parent=>Array
prevId=>58
previous=>Array
child=>Array
nextId=>104
next=>Array
children=>Array
level=>2

        $this->getNode
        foreach($this->data[$srcId] as $key=>$value)
            echo "$key=>$value<br>";
*/
    }

    // }}}

}
?>