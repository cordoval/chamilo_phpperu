<?php
namespace user;

use common\libraries\Path;
require_once Path :: get_common_path() . 'global.inc.php';

$handler = new LoginWebservice();
$handler->run();

?>
