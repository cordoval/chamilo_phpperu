<?php
namespace common\libraries;

require_once dirname(__FILE__) . '/../../../../global.inc.php';

$new_theme = Request :: get(' theme');

/*
 * Core application theme
 */
$core_applications = array(
        'webservice',
        'admin',
        'help',
        'reporting',
        'tracking',
        'repository',
        'user',
        'group',
        'rights',
        'home',
        'menu',
        'migration');

foreach ($core_applications as $core_application)
{

}
?>