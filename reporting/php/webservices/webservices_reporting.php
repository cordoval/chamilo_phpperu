<?php
use common\libraries\Path;

require_once Path ::get_common_path() . 'global.inc.php';
require_once dirname(__FILE__) . '/webservices_reporting.class.php';


ini_set('max_execution_time', - 1);
ini_set('memory_limit', - 1);

$handler = new WebServicesReporting();
$handler->run();
?>
