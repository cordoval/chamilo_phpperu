<?php
//  $Id: Memory_DBsimple.php 137 2009-11-09 13:24:37Z vanpouckesven $

include_once 'funcs.php';


    /*

        use this to build the db table

        CREATE TABLE test_tree (
            id int(11) NOT NULL auto_increment,
            parent int(11) NOT NULL default '0',
            name varchar(255) NOT NULL default '',
            PRIMARY KEY  (id)
        )


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
    // when reading the data from the db sort them by id, this is only for ensuring
    // for 'getNext' of "myElement/subElement" in this example to find "myElement/anotherSubElement"
    // you can simply sort it by "name" and it would be in alphabetical order
    // calling 'setupMemory' means to retreive a class, which works on trees,
    // that are temporarily stored in the memory, in an array
    // this means the entire tree is available at all time
    // consider the resource usage and it's not to suggested to work
    // on huge trees (upto 1000 elements it should be ok, depending on your environment and requirements)
    // using 'setupMemory'
    $config = array(
        'type' => 'Simple',
        'storage' => array(
            'name' => 'DB',
            'dsn' => 'mysql://root:hamstur@localhost/tree_test',
            // 'connection' => $db,
        ),
        'options' => array(
            'table' => 'test_tree',
            'order' =>  'id',
            'fields' => array(),
        ),
    );

    $tree =& Tree::factoryMemory($config);

    // add a new root element in the tree
    $parentId = $tree->add(array('name' => 'myElement'));

    // add an element under the new element we added
    $id = $tree->add(array('name' => 'subElement'), $parentId);

    // add another element under the parent element we added
    $id = $tree->add(array('name' => 'anotherSubElement'), $parentId);

    // call 'setup', to build the inner array, so we can work on the structure using the
    // given methods
    $tree->setup();

    dumpAllNicely('dump all after creation');

    // get the path of the last inserted element
    dumpHelper('$tree->getPath( '.$id.' )' , 'dump the path from "myElement/anotherSubElement"');

    print "tree->getIdByPath('myElement/subElement')=".$id = $tree->getIdByPath('myElement/subElement');
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
    dumpHelper('$tree->prevSibling('.$id.')' , 'dump the "previous" of "myElement/anotherSubElement"' , true);
    // you can also use:    $tree->data[$id]['previous']

    $id = $tree->getIdByPath('myElement/anotherSubElement');
    $tree->move($id , 0);
    $tree->setup(); // rebuild the structure again, since we had changed it
    dumpAllNicely( 'dump all, after "myElement/anotherSubElement" was moved under the root' );

    $moveId = $tree->getIdByPath('myElement');
    $id = $tree->getIdByPath('anotherSubElement');
    #$tree->move( $moveId , $id );
    #$tree->setup(); // rebuild the structure again, since we had changed it
    dumpAllNicely('dump all, after "myElement" was moved under the "anotherSubElement"');

    #$tree->setRemoveRecursively(true);
    #$tree->remove(0);
    #echo '<font color="red">ALL ELEMENTS HAVE BEEN REMOVED (uncomment this part to keep them in the DB after running this test script)</font>';

?>
