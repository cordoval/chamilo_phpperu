<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2005 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors:                                                             |
// +----------------------------------------------------------------------+
//
//  $Id: SQLnested.php 137 2009-11-09 13:24:37Z vanpouckesven $

require_once 'Tree/Tree.php';

/**
* This class implements methods to work on a tree saved using the nested
* tree model.
* explaination: http://research.calacademy.org/taf/proceedings/ballew/index.htm
*
* @access     public
* @package    Tree
*/
class Tree_Dynamic_SQLnested extends Tree
{
    // {{{ __construct()

    // the defined methods here are proposals for the implementation,
    // they are named the same, as the methods in the "Memory" branch.
    // If possible it would be cool to keep the same naming. And
    // if the same parameters would be possible too then it would be
    // even better, so one could easily change from any kind
    // of tree-implementation to another, without changing the source
    // code, only the setupXXX would need to be changed
    /**
      *
      *
      * @access     public
      * @version    2002/03/02
      * @param      string  the DSN for the DB connection
      * @return     void
      */
    function __construct($config)
    {
        parent :: __construct($config);
    }

    // }}}
    // {{{ Tree_Dynamic_DBnested()

    /**
     *
     *
     * @access     public
     * @version    2002/03/02
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      string  the DSN for the DB connection
     * @return     void
     */
    function __construct($config)
    {
        $this->conf = Tree::arrayMergeClobber($this->conf, $config['options']);
        $this->init($config);
    }

    // }}}
    // {{{ add()

    /**
     * add a new element to the tree
     * there are three ways to use this method
     * Method 1:
     * Give only the $parent_id and the $newValues will be inserted
     * as the first child of this parent
     * <code>
     * // insert a new element under the parent with the ID=7
     * $tree->add(array('name'=>'new element name'), 7);
     * </code>
     *
     * Method 2:
     * Give the $prevId ($parent_id will be dismissed) and the new element
     * will be inserted in the tree after the element with the ID=$prevId
     * the parent_id is not necessary because the prevId defines exactly where
     * the new element has to be place in the tree, and the parent is
     * the same as for the element with the ID=$prevId
     * <code>
     * // insert a new element after the element with the ID=5
     * $tree->add(array('name'=>'new'), 0, 5);
     * </code>
     *
     * Method 3:
     * neither $parent_id nor prevId is given, then the root element will be
     * inserted. This requires that programmer is responsible to confirm this.
     * This method does not yet check if there is already a root element saved!
     *
     * @access     public
     * @param   array   $newValues  this array contains the values that shall
     *                              be inserted in the db-table
     * @param   integer $parent_id   the id of the element which shall be
     *                              the parent of the new element
     * @param   integer $prevId     the id of the element which shall preceed
     *                              the one to be inserted use either
     *                              'parent_id' or 'prevId'.
     * @return   integer the ID of the element that had been inserted
     */
    function add($newValues, $parent_id = 0, $prevId = 0)
    {
        $left  = $this->_getColName('left');
        $right = $this->_getColName('right');
        $prevVisited = 0;

        // check the DB-table if the columns which are given as keys
        // in the array $newValues do really exist, if not remove them
        // from the array
        // FIXXME do the above described
        // if no parent and no prevId is given the root shall be added
        if ($parent_id || $prevId) {
            if ($prevId) {
                $element = $this->getElement($prevId);
                if (Tree::isError($element)) {
                    return $element;
                }
                // we also need the parent id of the element to write it in the db
                $parent_id = $element['parent_id'];
            } else {
                $element = $this->getElement($parent_id);
            }
            $newValues['parent_id'] = $parent_id;

            if (Tree::isError($element)) {
                return $element;
            }

            // get the "visited"-value where to add the new element behind
            // if $prevId is given, we need to use the right-value
            // if only the $parent_id is given we need to use the left-value
            // look at it graphically, that made me understand it :-)
            // See:
            // http://research.calacademy.org/taf/proceedings/ballew/sld034.htm
            $prevVisited = $prevId ? $element['right'] : $element['left'];

            // FIXXME start transaction here
            if (Tree::isError($err = $this->_add($prevVisited, 1))) {
                // FIXXME rollback
                //$this->_storage->rollback();
                return $err;
            }
        }

        // inserting _one_ new element in the tree
        $newData = array();
        // quote the values, as needed for the insert
        foreach ($newValues as $key => $value) {
            $type = $this->conf['fields'][$key]['type'];
            $newData[$this->_getColName($key)] = $this->_storage->quote($value, $type);
        }

        // set the proper right and left values
        $newData[$left] = $prevVisited + 1;
        $newData[$right] = $prevVisited + 2;

        // use sequences to create a new id in the db-table
        $nextId = $this->_storage->nextId($this->conf['table']);
        $query = sprintf('INSERT INTO %s (%s, %s) VALUES (%s, %s)',
                            $this->conf['table'],
                            $this->_getColName('id'),
                            implode(',', array_keys($newData)) ,
                            $this->_storage->quote($nextId, 'integer'),
                            implode(',', $newData)
                        );
        $res = $this->_storage->query($query);
        if (PEAR::isError($res)) {
            // rollback
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $res->getMessage());
        }
        // commit here

        return $nextId;
    }

    // }}}
    // {{{ _add()

    /**
     * this method only updates the left/right values of all the
     * elements that are affected by the insertion
     * be sure to set the parent_id of the element(s) you insert
     *
     * @param  int     this parameter is not the ID!!!
     *                 it is the previous visit number, that means
     *                 if you are inserting a child, you need to use the left-value
     *                 of the parent
     *                 if you are inserting a "next" element, on the same level
     *                 you need to give the right value !!
     * @param  int     the number of elements you plan to insert
     * @return mixed   either true on success or a Tree_Error on failure
     */
    function _add($prevVisited, $numberOfElements = 1)
    {
        $left  = $this->_getColName('left');
        $right = $this->_getColName('right');

        // update the elements which will be affected by the new insert
        $query = sprintf('UPDATE %s SET %s = %s + %s WHERE%s %s > %s',
                            $this->conf['table'],
                            $left,
                            $left,
                            $numberOfElements * 2,
                            $this->_getWhereAddOn(),
                            $left,
                            $prevVisited);
        if (PEAR::isError($res = $this->_storage->query($query))) {
            // FIXXME rollback
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $res->getMessage());
        }

        $query = sprintf('UPDATE %s SET %s = %s + %s WHERE%s %s > %s',
                            $this->conf['table'],
                            $right, $right,
                            $numberOfElements * 2,
                            $this->_getWhereAddOn(),
                            $right,
                            $prevVisited);
        $res = $this->_storage->query($query);
        if (PEAR::isError($res)) {
            // FIXXME rollback
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $res->getMessage());
        }
        return true;
    }

    // }}}
    // {{{ remove()

    /**
     * remove a tree element
     * this automatically remove all children and their children
     * if a node shall be removed that has children
     *
     * @access     public
     * @param      integer $id the id of the element to be removed
     * @return     boolean returns either true or throws an error
     */
    function remove($id)
    {
        $element = $this->getElement($id);
        if (Tree::isError($element)) {
            return $element;
        }

        // FIXXME start transaction
        //$this->_storage->autoCommit(false);
        $query = sprintf('DELETE FROM %s WHERE%s %s BETWEEN %s AND %s',
                            $this->conf['table'],
                            $this->_getWhereAddOn(),
                            $this->_getColName('left'),
                            $element['left'], $element['right']);
        $res = $this->_storage->query($query);
        if (PEAR::isError($res)) {
            // FIXXME rollback
            //$this->_storage->rollback();
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $res->getMessage());
        }

        if (Tree::isError($err = $this->_remove($element))) {
            // FIXXME rollback
            //$this->_storage->rollback();
            return $err;
        }
        return true;
    }

    // }}}
    // {{{ _remove()

    /**
     * removes a tree element, but only updates the left/right values
     * to make it seem as if the given element would not exist anymore
     * it doesnt remove the row(s) in the db itself!
     *
     * @see        getElement()
     * @access     private
     * @param      array   the entire element returned by "getElement"
     * @return     boolean returns either true or throws an error
     */
    function _remove($element)
    {
        $delta = $element['right'] - $element['left'] + 1;
        $left  = $this->_getColName('left');
        $right = $this->_getColName('right');

        // update the elements which will be affected by the remove
        $query = sprintf("UPDATE
                                %s
                            SET
                                %s = %s - $delta,
                                %s = %s - $delta
                            WHERE%s %s > %s",
                            $this->conf['table'],
                            $left, $left,
                            $right, $right,
                            $this->_getWhereAddOn(),
                            $left, $element['left']);
        $res = $this->_storage->query($query);
        if (PEAR::isError($res)) {
            // the rollback shall be done by the method calling this one
            // since it is only private we can do that
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $res->getMessage());
        }

        $query = sprintf("UPDATE
                                %s
                            SET %s = %s - $delta
                            WHERE
                                %s %s < %s
                              AND
                                %s > %s",
                            $this->conf['table'],
                            $right, $right,
                            $this->_getWhereAddOn(),
                            $left, $element['left'],
                            $right, $element['right']);
        $res = $this->_storage->query($query);
        if (PEAR::isError($res)) {
            // the rollback shall be done by the method calling this one
            // since it is only private
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $res->getMessage());
        }
        // FIXXME commit:
        // should that not also be done in the method calling this one?
        // like when an error occurs?
        //$this->_storage->commit();
        return true;
    }

    // }}}
    // {{{ move()

    /**
     * move an entry under a given parent or behind a given entry.
     * If a newPrevId is given the newparent_id is dismissed!
     * call it either like this:
     *  $tree->move(x, y)
     *  to move the element (or entire tree) with the id x
     *  under the element with the id y
     * or
     *  $tree->move(x, 0, y);   // ommit the second parameter by setting
     *  it to 0
     *  to move the element (or entire tree) with the id x
     *  behind the element with the id y
     * or
     *  $tree->move(array(x1,x2,x3), ...
     *  the first parameter can also be an array of elements that shall
     *  be moved. the second and third para can be as described above.
     *
     * If you are using the Memory_DBnested then this method would be invain,
     * since Memory.php already does the looping through multiple elements.
     * But if Dynamic_DBnested is used we need to do the looping here
     *
     * @version    2002/06/08
     * @access     public
     * @param      integer  the id(s) of the element(s) that shall be moved
     * @param      integer  the id of the element which will be the new parent
     * @param      integer  if prevId is given the element with the id idToMove
     *                      shall be moved _behind_ the element with id=prevId
     *                      if it is 0 it will be put at the beginning
     * @return     mixed    true for success, Tree_Error on failure
     */
    function move($idsToMove, $newparent_id, $newPrevId = 0)
    {
        settype($idsToMove, 'array');
        $errors = array();
        foreach ($idsToMove as $idToMove) {
            $ret = $this->_move($idToMove, $newparent_id, $newPrevId);
            if (Tree::isError($ret)) {
                $errors[] = $ret;
            }
        }
        // FIXXME the error in a nicer way, or even better
        // let the throwError method do it!!!
        if (count($errors)) {
            return Tree::raiseError(TREE_ERROR_UNKOWN_ERROR, null, null, serialize($errors));
        }
        return true;
    }

    // }}}
    // {{{ _move()

    /**
     * this method moves one tree element
     *
     * @see     move()
     * @version 2002/04/29
     * @access  public
     * @param   integer the id of the element that shall be moved
     * @param   integer the id of the element which will be the new parent
     * @param   integer if prevId is given the element with the id idToMove
     *                  shall be moved _behind_ the element with id=prevId
     *                  if it is 0 it will be put at the beginning
     * @return  mixed    true for success, Tree_Error on failure
     */
    function _move($idToMove, $newparent_id, $newPrevId = 0)
    {
        // do some integrity checks first
        if ($newPrevId) {
            // dont let people move an element behind itself, tell it
            // succeeded, since it already is there :-)
            if ($newPrevId == $idToMove) {
                return true;
            }
            if (Tree::isError($newPrevious = $this->getElement($newPrevId))) {
                return $newPrevious;
            }
            $newparent_id = $newPrevious['parent_id'];
        } else {
            if ($newparent_id == 0) {
                return Tree::raiseError(TREE_ERROR_UNKOWN_ERROR, null, null, 'no parent id given');
            }
            // if the element shall be moved under one of its children
            // return false
            if ($this->isChildOf($idToMove, $newparent_id)) {
                return Tree::raiseError(TREE_ERROR_UNKOWN_ERROR, null, null,
                            'can not move an element under one of its children');
            }
            // dont do anything to let an element be moved under itself
            // which is bullshit
            if ($newparent_id == $idToMove) {
                return true;
            }
            // try to retreive the data of the parent element
            if (Tree::isError($newParent = $this->getElement($newparent_id))) {
                return $newParent;
            }
        }
        // get the data of the element itself
        if (Tree::isError($element = $this->getElement($idToMove))) {
            return $element;
        }

        $numberOfElements = ($element['right'] - $element['left'] + 1) / 2;
        $prevVisited = $newPrevId ? $newPrevious['right'] : $newParent['left'];

        // FIXXME start transaction

        // add the left/right values in the new parent, to have the space
        // to move the new values in
        $err = $this->_add($prevVisited, $numberOfElements);
        if (Tree::isError($err)) {
            // FIXXME rollback
            //$this->_storage->rollback();
            return $err;
        }

        // update the parent_id of the element with $idToMove
        $err = $this->update($idToMove, array('parent_id' => $newparent_id));
        if (Tree::isError($err)) {
            // FIXXME rollback
            //$this->_storage->rollback();
            return $err;
        }

        // update the lefts and rights of those elements that shall be moved

        // first get the offset we need to add to the left/right values
        // if $newPrevId is given we need to get the right value,
        // otherwise the left since the left/right has changed
        // because we already updated it up there. We need to get them again.
        // We have to do that anyway, to have the proper new left/right values
        if ($newPrevId) {
            if (Tree::isError($temp = $this->getElement($newPrevId))) {
                // FIXXME rollback
                //$this->_storage->rollback();
                return $temp;
            }
            $calcWith = $temp['right'];
        } else {
            if (Tree::isError($temp = $this->getElement($newparent_id))) {
                // FIXXME rollback
                //$this->_storage->rollback();
                return $temp;
            }
            $calcWith = $temp['left'];
        }

        // get the element that shall be moved again, since the left and
        // right might have changed by the add-call
        if (Tree::isError($element = $this->getElement($idToMove))) {
            return $element;
        }
        // calc the offset that the element to move has
        // to the spot where it should go
        // correct the offset by one, since it needs to go inbetween!
        $offset = $calcWith - $element['left'] + 1;

        $left = $this->_getColName('left');
        $right = $this->_getColName('right');
        $query = sprintf("UPDATE
                                %s
                            SET
                                %s = %s + $offset,
                                %s = %s + $offset
                            WHERE
                                %s %s > %s
                                AND
                                %s < %s",
                            $this->conf['table'],
                            $right, $right,
                            $left, $left,
                            $this->_getWhereAddOn(),
                            $left, $element['left'] - 1,
                            $right, $element['right'] + 1);
        if (PEAR::isError($res = $this->_storage->query($query))) {
            // FIXXME rollback
            //$this->_storage->rollback();
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $res->getMessage());
        }

        // remove the part of the tree where the element(s) was/were before
        if (Tree::isError($err = $this->_remove($element))) {
            // FIXXME rollback
            //$this->_storage->rollback();
            return $err;
        }
        // FIXXME commit all changes
        //$this->_storage->commit();

        return true;
    }

    // }}}
    // {{{ update()

    /**
     * update the tree element given by $id with the values in $newValues
     *
     * @access     public
     * @param      int     the id of the element to update
     * @param      array   the new values, the index is the col name
     * @return     mixed   either true or an Tree_Error
     */
    function update($id, $newValues)
    {
        // just to be sure nothing gets screwed up :-)
        unset($newValues[$this->_getColName('left')]);
        unset($newValues[$this->_getColName('right')]);
        unset($newValues[$this->_getColName('parent_id')]);

        // updating _one_ element in the tree
        $values = array();
        foreach ($newValues as $key => $value) {
            $type = $this->conf['fields'][$key]['type'];
            $values[] = $this->_getColName($key) . ' = ' . $this->_storage->quote($value, $type);
        }
        $query = sprintf('UPDATE %s SET %s WHERE%s %s = %s',
                            $this->conf['table'],
                            implode(',', $values),
                            $this->_getWhereAddOn(),
                            $this->_getColName('id'),
                            $id);
        $res = $this->_storage->query($query);
        if (PEAR::isError($res)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $res->getMessage());
        }

        return true;
    }

    // }}}
    // {{{ update()

    /**
     * copy a subtree/node/... under a new parent or/and behind a given element
     *
     *
     * @access     public
     * @param      integer the ID of the node that shall be copied
     * @param      integer the new parent ID
     * @param      integer the new previous ID, if given parent ID will be omitted
     * @return     boolean true on success
     */
    function copy($id, $parent_id = 0, $prevId = 0)
    {
        return Tree::raiseError(TREE_ERROR_NOT_IMPLEMENTED, null, null,
                                'copy-method is not implemented yet!');
        // get element tree
        // $this->addTree
    }

    // }}}
    // {{{ getRoot()

    /**
     * get the root
     *
     * @access     public
     * @version    2002/03/02
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @return     mixed   either the data of the root element or an Tree_Error
     */
    function getRoot()
    {
        $query = sprintf('SELECT * FROM %s WHERE%s %s = 1',
                            $this->conf['table'],
                            $this->_getWhereAddOn(),
                            $this->_getColName('left'));
        $res = $this->_storage->queryRow($query, array());
        if (PEAR::isError($res)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $res->getMessage());
        }

        return !$res ? false : $this->_prepareResult($res);
    }

    // }}}
    // {{{ getElement()

    /**
     *
     *
     * @access     public
     * @version    2002/03/02
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      integer  the ID of the element to return
     *
     * @return  mixed    either the data of the requested element
     *                      or an Tree_Error
     */
    function getElement($id)
    {
        $query = sprintf('SELECT * FROM %s WHERE %s %s = %s',
                            $this->conf['table'],
                            $this->_getWhereAddOn(),
                            $this->_getColName('id'),
                            $id);
        $res = $this->_storage->queryRow($query, array());
        if (PEAR::isError($res)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $res->getMessage());
        }

        if (!$res) {
            return Tree::raiseError(TREE_ERROR_UNKOWN_ERROR, null, null, "Element with id $id does not exist!");
        }

        return $this->_prepareResult($res);
    }

    // }}}
    // {{{ getPath()

    /**
     * gets the path from the element with the given id down
     * to the root. The returned array is sorted to start at root
     * for simply walking through and retreiving the path
     *
     * @access public
     * @param integer the ID of the element for which the path shall be returned
     * @return mixed  either the data of the requested elements
     *                      or an Tree_Error
     */
    function getPath($id)
    {
        $query = $this->_getPathQuery($id);
        if (PEAR::isError($query)) {
            /// FIXME return real tree error
            return false;
        }

        $res = $this->_storage->queryAll($query, array(), false, false);
        if (PEAR::isError($res)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $res->getMessage());
        }

        return $this->_prepareResults($res);
    }

    // }}}
    // {{{ _getPathQuery()

    function _getPathQuery($id)
    {
        // subqueries would be cool :-)
        $curElement = $this->getElement($id);
        if (PEAR::isError($curElement)) {
            /// FIXME return real tree error
            return false;
        }

        $left = $this->_getColName('left');
        $query = sprintf('SELECT * FROM %s '.
                            'WHERE %s %s <= %s AND %s >= %s '.
                            'ORDER BY %s',
                            // set the FROM %s
                            $this->conf['table'],
                            // set the additional where add on
                            $this->_getWhereAddOn(),
                            // render 'left<=curLeft'
                            $left,  $curElement['left'],
                            // render right>=curRight'
                            $this->_getColName('right'), $curElement['right'],
                            // set the order column
                            $left);
        return $query;
    }

    // }}}
    // {{{ getLevel()

    function getLevel($id)
    {
        $query = $this->_getPathQuery($id);
        // i know this is not really beautiful ...
        $id = $this->_getColName('id');
        $replace = "SELECT COUNT($id) ";
        $query = preg_replace('/^select \* /i', $replace, $query);
        $res = $this->_storage->queryOne($query, 'integer');
        if (PEAR::isError($res)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $res->getMessage());
        }

        return $res - 1;
    }

    // }}}
    // {{{ getLeft()

    /**
     * gets the element to the left, the left visit
     *
     * @access     public
     * @version    2002/03/07
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      integer  the ID of the element
     * @return     mixed    either the data of the requested element
     *                      or an Tree_Error
     */
    function getLeft($id)
    {
        $element = $this->getElement($id);
        if (Tree::isError($element)) {
            return $element;
        }

        $query = sprintf('SELECT * FROM %s WHERE%s (%s = %s OR %s = %s)',
                            $this->conf['table'],
                            $this->_getWhereAddOn(),
                            $this->_getColName('right'), $element['left'] - 1,
                            $this->_getColName('left'),  $element['left'] - 1);
        $res = $this->_storage->queryRow($query, array());
        if (PEAR::isError($res)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $res->getMessage());
        }

        return $this->_prepareResult($res);
    }

    // }}}
    // {{{ getRight()

    /**
     * gets the element to the right, the right visit
     *
     * @access     public
     * @version    2002/03/07
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      integer  the ID of the element
     * @return     mixed    either the data of the requested element
     *                      or an Tree_Error
     */
    function getRight($id)
    {
        $element = $this->getElement($id);
        if (Tree::isError($element)) {
            return $element;
        }

        $query = sprintf('SELECT * FROM %s WHERE%s (%s = %s OR %s = %s)',
                            $this->conf['table'],
                            $this->_getWhereAddOn(),
                            $this->_getColName('left'),  $element['right'] + 1,
                            $this->_getColName('right'), $element['right'] + 1);
        $res = $this->_storage->queryRow($query, array());
        if (PEAR::isError($res)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $res->getMessage());
        }

        return $this->_prepareResult($res);
    }

    // }}}
    // {{{ getParent()

    /**
     * get the parent of the element with the given id
     *
     * @access     public
     * @version    2002/04/15
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      integer the ID of the element
     * @return     mixed    the array with the data of the parent element
     *                      or false, if there is no parent, if the element is
     *                      the root or an Tree_Error
     */
    function getParent($id)
    {
        $idName = $this->_getColName('id');
        $query = sprintf('SELECT
                                p.*
                            FROM
                                %s p,%s e
                            WHERE
                                %s e.%s = p.%s
                              AND
                                e.%s = %s',
                            $this->conf['table'], $this->conf['table'],
                            $this->_getWhereAddOn(' AND ', 'p'),
                            $this->_getColName('parent_id'),
                            $idName,
                            $idName,
                            $id);
        $res = $this->_storage->queryRow($query, array());
        if (PEAR::isError($res)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $res->getMessage());
        }

        return $this->_prepareResult($res);
    }

    // }}}
    // {{{ getChild()

    /**
     *
     *
     * @access     public
     * @version    2002/03/02
     * @param      integer  the ID of the element for which the children
     *                      shall be returned
     * @return     mixed   either the data of the requested element or an Tree_Error
     */
    function _getChild($id)
    {
        // subqueries would be cool :-)
        $curElement = $this->getElement($id);
        if (Tree::isError($curElement)) {
            return $curElement;
        }

        $query = sprintf('SELECT * FROM %s WHERE%s %s = %s',
                            $this->conf['table'],
                            $this->_getWhereAddOn(),
                            $this->_getColName('left'),
                            $curElement['left'] + 1);
        $res = $this->_storage->queryRow($query, array());
        if (PEAR::isError($res)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $res->getMessage());
        }
        return $this->_prepareResult($res);
    }

    // }}}
    // {{{ getChildren()

    /**
     * get the children of the given element or if the parameter is an array.
     * It gets the children of all the elements given by their ids
     * in the array.
     *
     * @access     public
     * @version    2002/04/15
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      mixed   (1) int     the id of one element
     *                     (2) array   an array of ids for which
     *                                 the children will be returned
     * @param      boolean if only the first child should be returned (only used when one id is passed)
     * @param      integer the children of how many levels shall be returned
     * @return     mixed   the array with the data of all children
     *                     or false, if there are none
     */
    function getChildren($ids, $oneChild = false, $levels = 1)
    {
        if ($oneChild) {
            $res = $this->_getChild($ids);
            return $res;
        }

        $id      = $this->_getColName('id');
        $parent  = $this->_getColName('parent_id');
        $left    = $this->_getColName('left');
        $where   = $this->_getWhereAddOn(' AND ', 'c');
        $orderBy = $this->getOption('order') ? $this->getOption('order') : $left;

        $res = array();
        for ($i = 1; $i < $levels + 1; $i++) {
            // if $ids is an array implode the values
            $getIds = is_array($ids) ? implode(',', $ids) : $ids;

            $query = sprintf('SELECT
                                    c.*
                                FROM
                                    %s c,%s e
                                WHERE
                                    %s e.%s = c.%s
                                  AND
                                    e.%s IN (%s) '.
                                'ORDER BY
                                    c.%s',
                                $this->conf['table'], $this->conf['table'],
                                $where,
                                $id,
                                $parent,
                                $id,
                                $getIds,
                                // order by left, so we have it in the order
                                // as it is in the tree if no 'order'-option
                                // is given
                                $orderBy
                       );
            $_res = $this->_storage->queryAll($query, array(), false, false);
            if (PEAR::isError($_res)) {
                return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $_res->getMessage());
            }

            // Column names are now unmapped
            $_res = $this->_prepareResults($_res);

            if ($levels > 1) {
                $ids = array();
            }

            // we use the id as the index, to make the use easier esp.
            // for multiple return-values
            $tempRes = array();
            foreach ($_res as $aRes) {
                ///FIXME This part might be replace'able with key'ed array return
                $tempRes[$aRes['id']] = $aRes;
                // If there are more levels requested then get the id for the next level
                if ($levels > 1) {
                    $ids[] = $aRes[$id];
                }
            }

            $res = array_merge($res, $tempRes);

            // quit the for-loop if there are no children in the current level
            if (!count($ids)) {
                break;
            }
        }

        return $res;
    }

    // }}}
    // {{{ nextSibling()

    /**
     * get the next element on the same level
     * if there is none return false
     *
     * @access     public
     * @version    2002/04/15
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      integer the ID of the element
     * @return     mixed   the array with the data of the next element
     *                     or false, if there is no next
     *                     or Tree_Error
     */
    function nextSibling($id)
    {
        $parent = $this->_getColName('parent_id');
        $query = sprintf('SELECT
                                n.*
                            FROM
                                %s n, %s e
                            WHERE
                                %s e.%s = n.%s - 1
                              AND
                                e.%s = n.%s
                              AND
                                e.%s = %s',
                            $this->conf['table'], $this->conf['table'],
                            $this->_getWhereAddOn(' AND ', 'n'),
                            $this->_getColName('right'),
                            $this->_getColName('left'),
                            $parent,
                            $parent,
                            $this->_getColName('id'),
                            $id);
        $res = $this->_storage->queryRow($query, array());
        if (PEAR::isError($res)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $res->getMessage());
        }

        return !$res ? false : $this->_prepareResult($res);
    }

    // }}}
    // {{{ previousSibling()

    /**
     * get the previous element on the same level
     * if there is none return false
     *
     * @access     public
     * @version    2002/04/15
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      integer the ID of the element
     * @return     mixed   the array with the data of the previous element
     *                     or false, if there is no previous
     *                     or a Tree_Error
     */
    function previousSibling($id)
    {
        $parent = $this->_getColName('parent_id');
        $query = sprintf('SELECT
                                p.*
                            FROM
                                %s p, %s e
                            WHERE
                                %s e.%s = p.%s + 1
                              AND
                                e.%s = p.%s
                              AND
                                e.%s = %s',
                            $this->conf['table'], $this->conf['table'],
                            $this->_getWhereAddOn(' AND ', 'p'),
                            $this->_getColName('left'),
                            $this->_getColName('right'),
                            $parent,
                            $parent,
                            $this->_getColName('id'),
                            $id);
        $res = $this->_storage->queryRow($query, array());
        if (PEAR::isError($res)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $res->getMessage());
        }

        return !$res ? false : $this->_prepareResult($res);
    }

    // }}}
    // {{{ isChildOf()

    /**
     * returns if $childId is a child of $id
     *
     * @abstract
     * @version    2002/04/29
     * @access     public
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      int     id of the element
     * @param      int     id of the element to check if it is a child
     * @return     boolean true if it is a child
     */
    function isChildOf($id, $childId)
    {
        // check simply if the left and right of the child are within the
        // left and right of the parent, if so it definitly is a child :-)
        $parent = $this->getElement($id);
        if (PEAR::isError($parent)) {
            /// FIXME return real tree error
            return false;
        }

        $child  = $this->getElement($childId);
        if (PEAR::isError($child)) {
            /// FIXME return real tree error
            return false;
        }

        if ($parent['left'] < $child['left']
            && $parent['right'] > $child['right'])
        {
            return true;
        }

        return false;
    }

    // }}}
    // {{{ getDepth()

    /**
     * return the maximum depth of the tree
     *
     * @version    2003/02/25
     * @access     public
     * @author "Denis Joloudov" <dan@aitart.ru>, Wolfram Kriesing <wolfram@kriesing.de>
     * @return integer the depth of the tree
     */
    function getDepth()
    {
        $left  = $this->_getColName('left');
        $right = $this->_getColName('right');
        // FIXXXME TODO!!!
        $query = sprintf('SELECT COUNT(*) FROM %s p, %s e '.
                            'WHERE %s (e.%s BETWEEN p.%s AND p.%s) AND '.
                            '(e.%s BETWEEN p.%s AND p.%s)',
                            $this->conf['table'], $this->conf['table'],
                            // first line in where
                            $this->_getWhereAddOn(' AND ','p'),
                            $left, $left, $right,
                            // second where line
                            $right, $left, $right
                            );
        $res = $this->_storage->queryOne($query, 'integer');
        if (PEAR::isError($res)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $res->getMessage());
        }

        if (!$res) {
            return false;
        }

        return $this->_prepareResult($res);
    }

    // }}}
    // {{{ hasChildren()

    /**
     * Tells if the node with the given ID has children.
     *
     * @version    2003/03/04
     * @access     public
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      integer the ID of a node
     * @return     boolean if the node with the given id has children
     */
    function hasChildren($id)
    {
        $element = $this->getElement($id);
        if (PEAR::isError($element)) {
            return false;
        }
        // if the diff between left and right > 1 then there are children
        return ($element['right'] - $element['left']) > 1;
    }

    // }}}
    // {{{ getIdByPath()

    /**
     * return the id of the element which is referenced by $path
     * this is useful for xml-structures, like: getIdByPath('/root/sub1/sub2')
     * this requires the structure to use each name uniquely
     * if this is not given it will return the first proper path found
     * i.e. there should only be one path /x/y/z
     * experimental: the name can be non unique if same names are in different levels
     *
     * @version    2003/05/11
     * @access     public
     * @author     Pierre-Alain Joye <paj@pearfr.org>
     * @param      string   $path       the path to search for
     * @param      integer  $startId    the id where to start the search
     * @param      string   $nodeName   the name of the key that contains
     *                                  the node name
     * @param      string   $seperator  the path seperator
     * @return     integer  the id of the searched element
     */
    function getIdByPath($path, $startId = 0, $nodeName = 'name', $separator = '/')
    // should this method be called getElementIdByPath ????
    // Yes, with an optional private paramater to get the whole node
    // in preference to only the id?
    {
        if ($separator == '') {
            return Tree::raiseError(TREE_ERROR_UNKOWN_ERROR, null, null,
                'getIdByPath: Empty separator not allowed');
        }

        if ($path == $separator) {
            if (Tree::isError($root = $this->getRoot())) {
                return $root;
            }
            return $root['id'];
        }

        if (!($colname = $this->_getColName($nodeName))) {
            return Tree::raiseError(TREE_ERROR_UNKOWN_ERROR, null, null,
                'getIdByPath: Invalid node name');
        }

        if ($startId != 0) {
            // If the start node has no child, returns false
            // hasChildren calls getElement. Not very good right
            // now. See the TODO
            $startElem = $this->getElement($startId);
            if (Tree::isError($startElem)) {
                return $startElem;
            }

            // No child? return
            if (!is_array($startElem)) {
                return null;
            }

            $rangeStart = $startElem['left'];
            $rangeEnd   = $startElem['right'];
            // Not clean, we should call hasChildren, but I do not
            // want to call getELement again :). See TODO
            $startHasChild = ($rangeEnd - $rangeStart) > 1 ? true : false;
            $cwd = '/' . $this->getPathAsString($startId);
        } else {
            $cwd = '/';
            $startHasChild = false;
        }

        $t = $this->_preparePath($path, $cwd, $separator);
        if (Tree::isError($t)) {
            return $t;
        }

        list($elems, $sublevels) = $t;
        $cntElems = count($elems);

        $query = '
            SELECT '
                . $this->_getColName('id') .
            ' FROM '
                . $this->conf['table'] .
            ' WHERE '
                . $colname;

        $element = $cntElems == 1 ? $elems[0] : $elems[$cntElems - 1];
        $query .= ' = ' . $this->_storage->quote($element, 'text');

        if ($startHasChild) {
            $query  .= ' AND ('.
                        $this->_getColName('left').' > '.$rangeStart.
                        ' AND '.
                        $this->_getColName('right').' < '.$rangeEnd.')';
        }

        $res = $this->_storage->queryOne($query, 'integer');
        if (PEAR::isError($res)) {
            return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $res->getMessage());
        }
        return $res ? (int)$res : false;
    }

    // }}}

    // {{{ _getWhereAddOn()
    /**
     *
     *
     * @access     private
     * @version    2002/04/20
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      string  the current where clause
     * @return     string  the where clause we want to add to a query
     */
    function _getWhereAddOn($addAfter = ' AND ', $tableName = '')
    {
        if (!empty($this->conf['whereAddOn'])) {
            return ' ' . ($tableName ? $tableName . '.' : '') . $this->conf['whereAddOn'] . $addAfter;
        }
        return '';
    }

    // }}}
    // {{{ getFirstRoot()

    // for compatibility to Memory methods
    function getFirstRoot()
    {
        return $this->getRoot();
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
     * @param      integer  $startId    the id where to start walking
     * @param      integer  $depth      this number says how deep into
     *                                  the structure the elements shall
     *                                  be retreived
     * @return     array    sorted as listed in the tree
     */
    function &getBranch($startId = 0, $depth = 0)
    {
//FIXXXME use getChildren()
        if ($startId) {
            $startNode = $this->getElement($startId);
            if (Tree::isError($startNode)) {
                return $startNode;
            }

        } else {
        }
    }
}

/*
 * Local Variables:
 * mode: php
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 */
?>
