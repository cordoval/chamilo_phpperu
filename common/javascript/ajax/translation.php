<?php
/**
 * $Id: translation.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.javascript.ajax
 */
require_once dirname(__FILE__) . '/../../global.inc.php';

$application = $_POST['application'];
$string = $_POST['string'];

Translation :: set_application($application);
echo Translation :: get($string);
?>