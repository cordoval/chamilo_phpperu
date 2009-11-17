<?php
//  $Id: move.php 137 2009-11-09 13:24:37Z vanpouckesven $

require_once 'UnitTest.php';

class tests_move extends UnitTest
{
    // check if we get the right ID, for the given path
    function test_MemoryDBnested()
    {
        $tree = $this->getMemoryDBnested();        
        $ret = $tree->move(5, 1);
        $tree->setup();

        // be sure true is returned
        $this->assertTrue($ret);
        // and check if the move succeeded, by checking the new parentId
        $parent = $tree->getParent(5);
        $this->assertEquals(1, $parent['id']);
    }

    function test_MemoryMDBnested()
    {
        $tree = $this->getMemoryMDBnested();        
        $ret = $tree->move(5, 1);
        $tree->setup();

        // be sure true is returned
        $this->assertTrue($ret);
        // and check if the move succeeded, by checking the new parentId
        $parent = $tree->getParent(5);
        $this->assertEquals(1, $parent['id']);
    }

    function test_MemoryDBnestedNoAction()
    {
        $tree = $this->getMemoryDBnested();        
//        $id = $tree->getIdByPath('/Root/child 2/child 2_2');
        $parent = $tree->getParent(5);
        $ret = $tree->move(5, 5);
        $tree->setup();
        // be sure true is returned
        $this->assertTrue($ret);
        $parent1 = $tree->getParent(5);
        $this->assertEquals($parent['id'], $parent1['id']);
    }

    function test_MemoryMDBnestedNoAction()
    {
        $tree = $this->getMemoryMDBnested();        
//        $id = $tree->getIdByPath('/Root/child 2/child 2_2');
        $parent = $tree->getParent(5);
        $ret = $tree->move(5, 5);
        $tree->setup();
        // be sure true is returned
        $this->assertTrue($ret);
        $parent1 = $tree->getParent(5);
        $this->assertEquals($parent['id'], $parent1['id']);
    }

    // do this for XML
            
    // do this for Filesystem

    // do this for DBsimple
    
    // do this for DynamicSQLnested
    function test_DynamicSQLnested()
    {
        $tree =& $this->getDynamicSQLnested();
        $ret = $tree->move(5, 1);

        // be sure true is returned
        $this->assertTrue($ret);
        // and check if the move succeeded, by checking the new parentId
        $parent = $tree->getParent(5);
        $this->assertEquals(1, $parent['id']);
    }

    function test_DynamicSQLnestedNoAction()
    {
        $tree =& $this->getDynamicSQLnested();
//        $id = $tree->getIdByPath('/Root/child 2/child 2_2');
        $parent = $tree->getParent(5);
        $ret = $tree->move(5, 5);
        // be sure true is returned
        $this->assertTrue($ret);
        $parent1 = $tree->getParent(5);
        $this->assertEquals($parent['id'], $parent1['id']);
    } 
}

?>
