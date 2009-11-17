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

$tree =& Tree::factoryMemory($config);
$tree->setup();

$root = $tree->getRoot();

echo str_repeat('-', $tree->getLevel($root['id'])) . $root['id'] . ' (parent: ' . $root['parent_id'] . ')<br />';
recursive($root['id']);

// Usually it's a good idea to do -1 on each level since there can only be one root node
function recursive($id)
{
    global $tree;
    $blah = $tree->getChildren($id);
    foreach ($blah as $row) {
         echo str_repeat('-', $tree->getLevel($row['id'])) . $row['id'] . ' (parent: ' . $row['parent_id'] . ')<br />';
         recursive($row['id']);
    }
}