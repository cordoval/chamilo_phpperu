<?php
/**
 * $Id: path.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.javascript.ajax
 */
require_once dirname(__FILE__) . '/../../global.inc.php';

$path = $_POST['path'];

echo Path :: get($path);
?>
