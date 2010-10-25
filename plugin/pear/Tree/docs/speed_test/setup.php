<?php
require_once 'Tree/Tree.php';

$config = array(
    'type' => 'Nested',
    'storage' => array(
        'name' => 'MDB2',
        'dsn' => 'mysql://root:hamstur@localhost/tree_test',
        // 'connection' =>
    ),
    'options' => array(
        'table' => 'nestedTree1',
        'order' =>  'id',
        'fields' => array(),
    ),
);

$tree =& Tree::factoryDynamic($config);


$tree->add(array('name' => 'c0') , 0);
$tree->add(array('name' => 'c1') , 1);
$tree->add(array('name' => 'c2') , 2);
$tree->add(array('name' => 'c3') , 3);

$tree->add(array('name' => 'c0') , 4);
$tree->add(array('name' => 'c1') , 5);
$tree->add(array('name' => 'c2') , 6);
$tree->add(array('name' => 'c3') , 7);

$tree->add(array('name' => 'c0') , 8);
$tree->add(array('name' => 'c1') , 9);
$tree->add(array('name' => 'c2') , 10);
$tree->add(array('name' => 'c3') , 11);

$tree->add(array('name' => 'c0') , 12);
$tree->add(array('name' => 'c1') , 13);
$tree->add(array('name' => 'c2') , 14);
$tree->add(array('name' => 'c3') , 15);

$tree->add(array('name' => 'c0') , 16);
$tree->add(array('name' => 'c1') , 17);
$tree->add(array('name' => 'c2') , 18);
$tree->add(array('name' => 'c3') , 19);