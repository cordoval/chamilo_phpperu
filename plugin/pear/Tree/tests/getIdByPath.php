<?php
//  $Id: getIdByPath.php 137 2009-11-09 13:24:37Z vanpouckesven $

require_once 'UnitTest.php';

class tests_getIdByPath extends UnitTest
{
    // check if we get the right ID, for the given path
    function test_MemoryDBnested()
    {
        $tree = $this->getMemoryDBnested();
        $id = $tree->getIdByPath('Root/child 2/child 2_2');

        $this->assertEquals(5, $id);
    }

    function test_MemoryMDBnested()
    {
        $tree = $this->getMemoryMDBnested();
        $id = $tree->getIdByPath('Root/child 2/child 2_2');
        
        $this->assertEquals(5, $id);
    }

    // do this for XML
            
    // do this for Filesystem

    // do this for DBsimple
    
    // do this for DynamicSQLnested
    function test_DynamicSQLnested()
    {
        $tree = $this->getDynamicSQLnested();
        $id = $tree->getIdByPath('/Root/child 2/child 2_2');

        $this->assertEquals(5, $id, 'This is not implemented, yet!!! (This test should fail ... for now)');
    }
}

?>
