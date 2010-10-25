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
//  $Id: Simple.php 137 2009-11-09 13:24:37Z vanpouckesven $

require_once 'Tree/Memory.php';

class Tree_Memory_Simple extends Tree_Memory {

    function setup()
    {
        $this->_storage->select();
    }

    function add($data, $parentId = 0)
    {
        $this->_storage->insert();
    }

    function remove($id)
    {
        // if the one to remove has children, get their id's to remove them too
        if ($this->hasChildren($id)) {
            $id = $this->walk(array('_remove', $this), $id, 'array');
        } else {
            $id = array($id);
        }

        $result = $this->_storage->remove();
        if (PEAR::isError($result)) {
           return $result;
        }
        
        return true;
    }

    function update($id, $data)
    {
        $this->_storage->update();
    }

    function move($idToMove, $newParent, $newPrevId = 0)
    {
        $this->_storage->update();
    }
}
?>