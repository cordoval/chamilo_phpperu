<?php
//  $Id: Dynamic_MDBnested.php 137 2009-11-09 13:24:37Z vanpouckesven $

// use nested_tree.sql to build the db table

ini_set('error_reporting', E_ALL);

require_once 'Tree/Tree.php';

$config = array(
    'type' => 'Nested',
    'storage' => array(
        'name' => 'MDB',
        'dsn' => 'mysql://root:hamstur@localhost/tree_test',
        // 'connection' =>
    ),
    'options' => array(
        'table' => 'nestedTree',
        'order' =>  'id',
        'fields' => array(),
    ),
);

$tree =& Tree::factoryDynamic($config);

$show[] = '$tree->getRoot()';
$show[] = '$tree->getElement( 1 )';
$show[] = '$tree->getChildren(1, true)';
$show[] = '$tree->getPath( 7 )';
$show[] = '$tree->getPath( 2 )';
// $show[] = '$tree->add( array("name"=>"c0") , 5 )';
// $show[] = '$tree->remove( $res )';  // remove the last element that was added in the line before :-)
$show[] = '$tree->getRight( 5 )';
$show[] = '$tree->getLeft( 5 )';
$show[] = '$tree->getChildren( 1 )';
$show[] = '$tree->getParent( 2 )';
$show[] = '$tree->nextSibling( 2 )';
$show[] = '$tree->nextSibling( 4 )';
$show[] = '$tree->nextSibling( 8 )';
$show[] = '$tree->previousSibling( 2 )';
$show[] = '$tree->previousSibling( 4 )';
$show[] = '$tree->previousSibling( 8 )';

$show[] = '$tree->move( 4,3 )';


foreach ($show as $aRes) {
    echo "<strong>$aRes</strong><br />";
    eval("\$res=".$aRes.';');
    if ($res == false) {
        print "false";
    } else {
        echo '<pre>';
        print_r($res);
        echo '</pre>';
    }
    echo '<br /><br />';
}
?>

<a href="http://research.calacademy.org/taf/proceedings/ballew/sld029.htm">the tree structure visualisation</a>
