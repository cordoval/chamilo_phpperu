<?php
require_once (dirname(__FILE__) . '/../../../common/global.inc.php');

ini_set('max_execution_time', - 1);
ini_set('memory_limit', - 1);

$handler = new WebServicesGroup();
$handler->run();
?>
