<?php

use common\libraries\ChamiloRestServer;

// TEST SCRIPT
include_once ('common/global.inc.php');

$rest_server = new ChamiloRestServer();
$rest_server->handle();

?>
