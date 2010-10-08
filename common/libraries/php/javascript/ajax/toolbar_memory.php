<?php
/**
 * $Id: toolbar_memory.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.javascript.ajax
 */
require_once dirname(__FILE__) . '/../../global.inc.php';

$state = $_POST['state'];
$_SESSION['toolbar_state'] = $state;

?>