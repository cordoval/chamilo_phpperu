<?php

namespace application\cda;

use common\libraries\WebApplication;
use common\libraries\Path;

require_once dirname(__FILE__) . '/../../../../../common/global.inc.php';
require_once WebApplication :: get_application_class_lib_path('cda') . 'util/variable_scanner/file_variable_scanner.class.php';

set_time_limit(0);

$scanner = new FileVariableScanner();

/*$applications = array('admin', 'common', 'group', 'help', 'home', 'install', 'menu', 'migration', 'reporting', 'repository', 'rights', 'tracking', 'user', 'webservice',
        'alexia', 'assessment', 'cda', 'distribute', 'forum', 'laika', 'linker', 'personal_calendar', 'personal_messenger', 'portfolio', 'profiler', 'reservations', 'search_portal', 'webconferencing', 'weblcms', 'wiki');*/

$applications = array('common');

foreach ($applications as $application)
{
    $scanner->scan_application($application, true);
}

?>