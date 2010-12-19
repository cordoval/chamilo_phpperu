<?php
namespace application\weblcms;

require_once (dirname(__FILE__) . '/../../../../common/global.inc.php');
require_once dirname(__FILE__) . '/webservices_course_class.php';

ini_set('max_execution_time', - 1);
ini_set('memory_limit', - 1);

$handler = new WebServicesCourse();
$handler->run();

?>
