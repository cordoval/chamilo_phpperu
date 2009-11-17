<?php
/**
 * $Id: index.php 225 2009-11-13 14:43:20Z vanpouckesven $
 * @package install.lib
 */

session_start();

$cidReset = true;
$this_section = 'install';

require_once dirname(__FILE__) . '/../common/filesystem/path.class.php';
require_once dirname(__FILE__) . '/../common/utilities.class.php';
ini_set('include_path', realpath(Path :: get_plugin_path() . 'pear'));

ini_set("memory_limit", "-1"); // Geen php-beperkingen voor geheugengebruik
ini_set("max_execution_time", "7200"); // Twee uur moet voldoende zijn...
//error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', '0');

function __autoload($classname)
{
	$autoloaders = array(Path :: get_common_path() . '/common_autoloader.class.php', Path :: get_repository_path() . 'repository_autoloader.class.php',
						 Path :: get_user_path() . 'user_autoloader.class.php', Path :: get_admin_path() . 'admin_autoloader.class.php',
					     Path :: get_group_path() . 'group_autoloader.class.php', Path :: get_help_path() . 'help_autoloader.class.php',
					     Path :: get_home_path() . 'home_autoloader.class.php', Path :: get_menu_path() . 'menu_autoloader.class.php',
					     Path :: get_reporting_path() . 'reporting_autoloader.class.php', Path :: get_rights_path() . 'rights_autoloader.class.php',
					     Path :: get_tracking_path() . 'tracking_autoloader.class.php', Path :: get_webservice_path() . 'webservice_autoloader.class.php',
					     Path :: get_application_library_path() . 'application_common_autoloader.class.php');
	
	foreach($autoloaders as $autoloader)
	{
		require_once $autoloader;
		
		$classn = substr(basename($autoloader), 0, -10);
		$classname_upp = Utilities :: underscores_to_camelcase($classn);
		$class = new $classname_upp;
		
		if($class->load($classname))
			break;
	}
}

require_once dirname(__FILE__) . '/lib/install_manager/install_manager.class.php';
Translation :: set_application($this_section);
Translation :: set_language('english');

try
{
    $application = CoreApplication :: factory('install');
    $application->run();
}
catch (Exception $exception)
{
    Application :: display_header();
    Display :: error_message($exception->getMessage());
    Application :: display_footer();
}
?>