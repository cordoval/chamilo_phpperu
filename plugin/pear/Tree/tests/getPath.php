<?php
//  $Id: getPath.php 137 2009-11-09 13:24:37Z vanpouckesven $

require_once 'UnitTest.php';

class tests_getPath extends UnitTest
{
    // check if we get the right ID, for the given path
    function test_MemoryDBnested()
    {
        $tree =& $this->getMemoryDBnested();        
        $this->_testPath($tree);
    }

    function test_MemoryMDBnested()
    {
        $tree =& $this->getMemoryMDBnested();        
        $this->_testPath($tree);
    }

    // do this for XML
            
    // do this for Filesystem

    // do this for DBsimple
    
    // do this for DynamicSQLnested
    function test_DynamicSQLnested()
    {
        $tree =& $this->getDynamicSQLnested();
        $this->_testPath($tree);
    }
    
    function _testPath(&$tree)
    {
//        $id = $tree->getIdByPath('/Root/child 2/child 2_2');
        $id = 5;
        $path = $tree->getPath($id);

        $this->assertEquals(3, sizeof($path));
        $this->assertEquals('Root', $path[0]['name']);
        $this->assertEquals('child 2', $path[1]['name']);
        $this->assertEquals('child 2_2', $path[2]['name']);
    }


}

?>
