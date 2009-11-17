<?php
//  $Id: Memory_DBnested.php 137 2009-11-09 13:24:37Z vanpouckesven $

include_once 'funcs.php';


    /*

        use this to build the db table

        CREATE TABLE Memory_nestedTree (
            id int(11) NOT NULL default '0',
            name varchar(255) NOT NULL default '',
            l int(11) NOT NULL default '0',
            r int(11) NOT NULL default '0',
            parent int(11) NOT NULL default '0',
            comment varchar(255) NOT NULL default '',
            PRIMARY KEY  (id)
        );

        This example demonstrates how to manage trees
        that are saved in a DB, it uses a very simple
        DB-structure, not nested trees (ok, that sucks, but it can be implemented :-) )

        it reads out the entire tree upon calling the method
        'setup', then you can work on the tree in whichever way
        you want, just have a look at the examples
        there are different ways to achieve things,
        i will try to demonstrate (all of) them

    */

    require_once 'Tree/Tree.php';

    // define the DB-table where the data shall be read from
    // calling 'setupMemory' means to retreive a class, which works on trees,
    // that are temporarily stored in the memory, in an array
    // this means the entire tree is available at all time !!!
    // consider the resource usage and it's not to suggested to work
    // on huge trees (upto 1000 elements it should be ok, depending on your environment and requirements)
    // use the nested DB schema, which is actually implemented in Dynamic/DBnested
    // the class Memory/DBnested is only kind of a wrapper to read the entire tree
    // and let u work on it, which to use should be chosen on case by case basis

$config = array(
    'type' => 'Nested',
    'storage' => array(
        'name' => 'DB',
        'dsn' => 'mysql://root:hamstur@localhost/tree_test',
        // 'connection' =>
    ),
    'options' => array(
        'table' => 'nestedTree',
        'order' =>  'id',
        'fields' => array(),
        'whereAddOn' => "comment=''"
    ),
);


    // using 'setupMemory'
    $tree =& Tree::factoryMemory($config);

    // add a new root element in the tree
    #$rootId = $tree->add(array('name' => 'myElement'));

    // add an element under the new element we added
    #$id = $tree->add(array('name' => 'subElement') , $rootId);

    // add another element under the parent element we added
    #$id = $tree->add(array('name' => 'anotherSubElement') , $rootId , $id);
$id = 0;
    // call 'setup', to build the inner array, so we can work on the structure using the
    // given methods
    $tree->setup();

    dumpAllNicely('dump all after creation');

    // get the path of the last inserted element
    dumpHelper('$tree->getPath( '.$id.' )' , 'dump the path from "myElement/anotherSubElement"');

    $id = $tree->getIdByPath('myElement/subElement');
    dumpHelper('$tree->getParent('.$id.')' , 'dump the parent of "myElement/subElement"' , true);
    // you can also use:    $tree->data[$id]['parent']

    $id = $tree->getIdByPath('myElement');
    dumpHelper('$tree->getChildren('.$id.', true)' , 'dump the child of "myElement"' , true);
    // you can also use:    $tree->data[$id]['child']

    $id = $tree->getIdByPath('myElement');
    dumpHelper('$tree->getChildren('.$id.')' , 'dump the children of "myElement"');
    // you can also use:    $tree->data[$id]['children']

    $id = $tree->getIdByPath('myElement/subElement');
    dumpHelper('$tree->nextSibling('.$id.')' , 'dump the "next" of "myElement/subElement"' , true);
    // you can also use:    $tree->data[$id]['next']

    $id = $tree->getIdByPath('myElement/anotherSubElement');
    dumpHelper('$tree->previousSibling('.$id.')' , 'dump the "previous" of "myElement/anotherSubElement"' , true);
    // you can also use:    $tree->data[$id]['previous']

    $id = $tree->getIdByPath('myElement');
    $element = $tree->data[$id]['child']['next']['parent']; // refer to yourself again, in a very complicated way :-)
    dumpHelper('$element[\'id\']' , 'demo of using the internal array, for referencing tree-nodes, see the code');

    $id = $tree->getIdByPath('myElement');
    $element = $tree->data[$id]['child']['next']; // refer to the second child of 'myElement'
    dumpHelper('$element[\'id\']' , 'demo2 of using the internal array, for referencing tree-nodes, see the code');

    $id = $tree->getIdByPath('myElement/anotherSubElement');
    $tree->move($id , 0);
    $tree->setup(); // rebuild the structure again, since we had changed it
    dumpAllNicely( 'dump all, after "myElement/anotherSubElement" was moved under the root' );

    $moveId = $tree->getIdByPath('myElement');
    $id = $tree->getIdByPath('anotherSubElement');
    $tree->move($moveId , $id);
    $tree->setup(); // rebuild the structure again, since we had changed it
    dumpAllNicely( 'dump all, after "myElement" was moved under the "anotherSubElement"' );

    $tree->setRemoveRecursively(true);
    $tree->remove($rootId);
    echo '<font color="red">ALL ELEMENTS HAVE BEEN REMOVED (uncomment this part to keep them in the DB after running this test script)</font>';


echo '<br /><br />';

?>
