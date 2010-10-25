<?php
//  $Id: remove.php 137 2009-11-09 13:24:37Z vanpouckesven $

require_once 'UnitTest.php';

class tests_remove extends UnitTest
{
/*
    function test_MemoryDBnested()
    {
        $tree = $this->getMemoryDBnested();        
        $ret=$tree->remove(5);
        $tree->setup();

        // be sure true is returned
        $this->assertTrue($ret);
        // and check if the move succeeded, by checking the new parentId
        //problem here is that memory returns another return value for a not existing element ... shit        
        $this->assertEquals(x, $tree->getElement(5));
    }
*/

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
        $ret = $tree->remove(5);

        // be sure true is returned
        $this->assertTrue($ret);
        // and check if the element doesnt exist anymore ... this is not 100% sure, since the 
        // returned error message is a string :-(
        $this->assertTrue(Tree::isError($tree->getElement(5)));
    }
}

?>
