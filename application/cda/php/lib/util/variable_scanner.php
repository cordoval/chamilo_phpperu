<?php

namespace application\cda;

use common\libraries\WebApplication;

require_once dirname(__FILE__) . '/../../../../../common/global.inc.php';
require_once WebApplication :: get_application_class_lib_path('cda') . 'util/variable_scanner.class.php';

set_time_limit(0);

$scanner = new VariableScanner(array('component', 'translations'), 'file');

$core_applications = array('admin', 'common', 'group', 'help', 'home', 'install', 'menu', 'migration', 'reporting', 'repository', 'rights', 'tracking', 'user', 'webservice');
$web_applications = WebApplication :: load_all_from_filesystem(false, false);
$applications = array_merge($core_applications, $web_applications);

$start = microtime(true);
$applications = array('cda');
foreach ($applications as $application)
{
    if($application == 'lib') continue;

    echo 'Scanning application: ' . $application . '<br />';
    $scanner->scan_application($application);
}

$end = microtime(true);
$total = $end - $start;

echo 'Time: ' . $total . 's';
?>