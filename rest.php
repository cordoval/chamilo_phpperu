<?php

use common\libraries\RestServer;

// TEST SCRIPT
include_once ('common/global.inc.php');

$rest_server = new RestServer();
$rest_server->handle();

?>
