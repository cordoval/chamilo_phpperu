<?php
//  $Id: UnitTest.php 137 2009-11-09 13:24:37Z vanpouckesven $

require_once 'DB.php';
require_once 'PHPUnit.php';

class UnitTest extends PhpUnit_TestCase
{
    function setUp()
    {
        // common factory, factory the table structure and data in the db
        // (this actually also does the tearDown, since we have the DROP TABLE queries in the factory too
        require 'sql.php'; 
        $db = DB::connect(DB_DSN);

        foreach ($dbStructure[$db->phptype]['setup'] as $aQuery) {
            $ret = $db->query($aQuery);
            if (DB::isError($ret)) {
                die($ret->getUserInfo());
            }
        }
        
        $this->setLooselyTyped(true);
    }

    function tearDown()
    {
/*        global $dbStructure;

        $querytool = new Common();
        foreach ($dbStructure[$querytool->db->phptype]['tearDown'] as $aQuery) {
//print "$aQuery<br><br>";        
            if (DB::isError($ret=$querytool->db->query($aQuery))) {
                die($ret->getUserInfo());
            }
        }
*/        
    }
    
    function &getMemoryDBnested()
    {
        $config = array(
            'container' => 'Memory',
            'type' => 'Nested',
            'storage' => array(
                'name' => 'DB',
                'dsn' => DB_DSN,
                // 'connection' =>
            ),
            'options' => array(
                'table' => TABLE_TREENESTED,
                'order' =>  'id',
                'fields' => array(
                    'comment' => array('type' => 'text', 'name' => 'comment'),
                ),
            ),
        );
        $tree = Tree::factory($config);
        $tree->setup();
        return $tree;
    }
    
    function &getDynamicSQLnested($name = 'DB')
    {
        $config = array(
            'container' => 'Dynamic',
            'type' => 'Nested',
            'storage' => array(
                'name' => $name,
                'dsn' => DB_DSN,
                // 'connection' =>
            ),
            'options' => array(
                'table' => TABLE_TREENESTED,
                'order' =>  'id',
                'fields' => array(
                    'comment' => array('type' => 'text', 'name' => 'comment'),
                ),
            ),
        );
        $tree = Tree::factory($config);

        return $tree;
    }
 
    function &getMemoryMDBnested()
    {
        $config = array(
            'container' => 'Memory',
            'type' => 'Nested',
            'storage' => array(
                'name' => 'MDB',
                'dsn' => DB_DSN,
                // 'connection' =>
            ),
            'options' => array(
                'table' => TABLE_TREENESTED,
                'order' =>  'id',
                'fields' => array(
                    'comment' => array('type' => 'text', 'name' => 'comment'),
                ),
            ),
        );
        $tree = Tree::factory($config);
        $tree->setup();
        return $tree;
    }
}

?>
