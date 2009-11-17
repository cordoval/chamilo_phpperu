<?php
//  $Id: index.php 137 2009-11-09 13:24:37Z vanpouckesven $

require_once 'PHPUnit.php';
require_once 'PHPUnit/GUI/HTML.php';

define('DB_DSN',           'mysql://root:hamstur@localhost/test');
define('TABLE_TREENESTED', 'TreeNested');

require_once 'Tree/Tree.php';

//
//  run the test suite
//
require_once 'PHPUnit/GUI/SetupDecorator.php';
$gui = new PHPUnit_GUI_SetupDecorator(new PHPUnit_GUI_HTML());
$gui->getSuitesFromDir(dirname(__FILE__),'.*\.php', array('UnitTest.php', 'index.php', 'sql.php'));
$gui->show();

//print_r($errors);

?>