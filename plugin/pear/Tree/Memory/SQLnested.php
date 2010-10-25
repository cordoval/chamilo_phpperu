<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors:                                                             |
// +----------------------------------------------------------------------+
//
//  $Id: SQLnested.php 137 2009-11-09 13:24:37Z vanpouckesven $

require_once 'Tree/Dynamic/SQLnested.php';

/**
 *
 *
 *   @access     public
 *   @author
 *   @package    Tree
 */
class Tree_Memory_SQLnested extends Tree_Dynamic_SQLnested
{
    /**
     * retreive all the data from the db and prepare the data so the structure
     * can be built in the parent class
     *
     * @version 2002/04/20
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   array   the result of a query which retreives (all)
     *                  the tree data from a DB
     * @return  array   the result
     */
    function setup($res = null)
    {
        if ($res == null) {
            //
            $whereAddOn = '';
            if ($this->conf['whereAddOn']) {
                $whereAddOn = 'WHERE '.$this->conf['whereAddOn'];
            }

            //
            $orderBy = 'left';
            if ($order = $this->conf['order']) {
                $orderBy = $order;
            }

            // build the query this way, that the root, which has no parent
            // (parent_id=0) is first
            $query = sprintf('SELECT * FROM %s %s ORDER BY %s',
                                $this->conf['table'],
                                $whereAddOn,
                                // sort by the left-column, so we have the data
                                //sorted as it is supposed to be :-)
                                $this->_getColName($orderBy)
                                );
            $res = $this->_storage->queryAll($query, array(), false, false);
            if (PEAR::isError($res)) {
                return Tree::raiseError(TREE_ERROR_DB_ERROR, null, null, $res->getMessage());
            }
        }

        return $this->_prepareResults($res);
    }

}

?>