<?php // $Id: global.inc.php 187 2009-11-13 10:31:25Z vanpouckesven $
/**
==============================================================================
 * It is recommended that ALL chamilo scripts include this important file.
 * This script manages
 * - http get, post, post_files, session, server-vars extraction into global namespace;
 *   (which doesn't occur anymore when servertype config setting is set to test,
 *    and which will disappear completely in Chamilo 1.6.1)
 * - selecting the main database;
 * - include of language files.
 *
 * @package common
==============================================================================
 */

// Determine the directory path where this current file lies
// This path will be useful to include the other intialisation files

$includePath = dirname(__FILE__);

// include the main Chamilo platform configuration file
$main_configuration_file_path = $includePath . '/configuration/configuration.php';
$already_installed = false;
if (file_exists($main_configuration_file_path))
{
    require_once ($main_configuration_file_path);
    $already_installed = true;
}

$error_message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<title>Chamilo isn\'t installed ?!</title>
		<link rel="stylesheet" href="layout/aqua/css/common.css" type="text/css"/>
	</head>
	<body dir="ltr">
		<div id="outerframe">
			<div id="header">
				<div id="header1">
					<div class="banner"><span class="logo"></span><span class="text">Chamilo</span></div>
					<div class="clear">&nbsp;</div>
				</div>
				<div class="clear">&nbsp;</div>
			</div>

			<div id="main" style="min-height: 300px;">' . "\n";

$version = phpversion();

if ($version >= 5.2)
{
    $error_message .= '				<div class="normal-message" style="margin-bottom: 39px; margin-top: 30px;">From the looks of it, Chamilo 2.0 is currently not installed on your system.<br /><br />Please check your database and/or configuration files if you are certain the platform was installed correctly.<br /><br />If you\'re starting Chamilo for the first time, you may want to install the platform first by clicking the button below. Alternatively, you can read the installation guide, visit ChamiloPlanet.org for more information or go to the community forum if you need support.
				</div>
				<div class="clear">&nbsp;</div>
				<div style="text-align: center;"><a class="button positive_button" href="install/">Install Chamilo 2.0</a><a class="button normal_button read_button" href="documentation/installation_guide.html">Read the installation guide</a><a class="button normal_button surf_button" href="http://www.chamilo.org/">Visit ChamiloPlanet.org</a><a class="button normal_button help_button" href="http://www.chamilo.org/forum/">Get support</a></div>' . "\n";
}
else
{
    $error_message .= '				<div class="error-message">Your version of PHP is not recent enough to use chamilo 2.0.
					   <br /><a href="http://www.php.net">Please upgrade to PHP version 5.2 or higher</a></div><br /><br />' . "\n";
}

$error_message .= '			</div>

			<div id="footer">
				<div id="copyright">
					<div class="logo">
					<a href="http://www.chamilo.org"><img src="layout/aqua/images/common/logo_footer.png" /></a>
					</div>
					<div class="links">
						<a href="http://www.chamilo.org">http://www.chamilo.org</a>&nbsp;|&nbsp;&copy;&nbsp;2009
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
	</body>
</html>';

//
if (! $already_installed)
{
    die($error_message);
}

// Add the path to the pear packages to the include path
require_once dirname(__FILE__) . '/filesystem/path.class.php';
require_once dirname(__FILE__) . '/utilities.class.php';
ini_set('include_path', realpath(Path :: get_plugin_path() . 'pear') . PATH_SEPARATOR . realpath(Path :: get_plugin_path() . 'google/library'));

function __autoload($classname)
{
	$autoloaders = array(dirname(__FILE__) . '/common_autoloader.class.php', Path :: get_repository_path() . 'repository_autoloader.class.php',
					     Path :: get_user_path() . 'user_autoloader.class.php', Path :: get_admin_path() . 'admin_autoloader.class.php',
					     Path :: get_group_path() . 'group_autoloader.class.php', Path :: get_help_path() . 'help_autoloader.class.php',
					     Path :: get_home_path() . 'home_autoloader.class.php', Path :: get_menu_path() . 'menu_autoloader.class.php',
					     Path :: get_migration_path() . 'migration_autoloader.class.php',
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

spl_autoload_register('__autoload');

require_once 'MDB2.php';

// Start session


Session :: start($already_installed);

// Test database connection
$connection = Connection :: get_instance()->get_connection();

if (MDB2 :: isError($connection))
{
    die($error_message);
}
unset($error_message);

/*
--------------------------------------------
  CHAMILO CONFIG SETTINGS
--------------------------------------------
*/

if (PlatformSetting :: get('server_type') == 'test')
{
    /*
	--------------------------------------------
	Server type is test
	- high error reporting level
	- only do addslashes on $_GET and $_POST
	--------------------------------------------
	*/

	if (phpversion() >= 5.3)
	{
		error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
	}
	else
	{
		error_reporting(E_ALL & ~ E_NOTICE);
	}

    //Addslashes to all $_GET variables
    foreach ($_GET as $key => $val)
    {
        if (! ini_get('magic_quotes_gpc'))
        {
            if (is_string($val))
            {
                //Request :: set_get($key,addslashes($val))
                Request :: set_get($key, addslashes($val));
            }
        }
    }

    //Addslashes to all $_POST variables
    foreach ($_POST as $key => $val)
    {
        if (! ini_get('magic_quotes_gpc'))
        {
            if (is_string($val))
            {
                $_POST[$key] = addslashes($val);
            }
        }
    }
}
else
{
    /*
	--------------------------------------------
	Server type is not test
	- normal error reporting level
	- full fake register globals block
	--------------------------------------------
	*/
	// TODO: Restore the normal error reporting for production software release.
    //error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
	// The following error reporting setting is for software under development, these lines are to be disabled.
	if (phpversion() >= 5.3)
	{
		error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
	}
	else
	{
		error_reporting(E_ALL & ~ E_NOTICE);
	}
}

/*
 * Handle login and logout
 * (Previously in local.inc.php)
 */

$language_interface = LocalSetting :: get('platform_language');
if(!AdminDataManager :: is_language_active($language_interface))
{
	$language_interface = PlatformSetting :: get('platform_language');
}

// TODO: Are these includes still necessary ?

// Login
if (isset($_POST['login']))
{
    $user = UserDataManager :: login($_POST['login'], $_POST['password']);
    if (get_class($user) == 'User')
    {
        Session :: register('_uid', $user->get_id());
        Event :: trigger('login', 'user', array('server' => $_SERVER, 'user' => $user));

        $request_uri = Session :: retrieve('request_uri');

        if ($request_uri)
        {
            $request_uris = explode("/", $request_uri);
            $request_uri = array_pop($request_uris);
            header('Location: ' . $request_uri);
        }

        $login_page = PlatformSetting :: get('page_after_login');
        if ($login_page == 'home')
        {
            header('Location: index.php');
        }
        else
        {
            header('Location: run.php?application=' . $login_page);
        }
    }
    else
    {
        Session :: unregister('_uid');
        header('Location: index.php?loginFailed=1&message=' . $user);
        exit();
    }
}
else
{
    Session :: unregister('request_uri');
}

set_error_handler('handle_error');

// Log out
if (Request :: get('logout'))
{
    $query_string = '';
    if (! empty($_SESSION['user_language_choice']))
    {
        $query_string = '?language=' . $_SESSION['user_language_choice'];
    }

    $user_id = Session :: get_user_id();

    if (isset($user_id))
    {
        $udm = UserDataManager :: get_instance();
        $user = $udm->retrieve_user(Session :: get_user_id());

        $udm = UserDataManager :: logout();
        Event :: trigger('logout', 'user', array('server' => $_SERVER, 'user' => $user));
    }

    header("Location: index.php");
    exit();
}
//unset($_SESSION['_uid']);


if (Request :: get('adminuser'))
{
    $checkurl = Session :: retrieve('checkChamiloURL');
    $admin_user = Session :: retrieve('_as_admin');
    Session :: clear();
    Session :: register('_uid', $admin_user);
    Session :: register('checkChamiloURL', $checkurl);
}

$user = Session :: get_user_id();
if ($user)
{
	Event :: trigger('online', AdminManager :: APPLICATION_NAME, array('user' => $user));
}

if (isset($_SESSION['_uid']))
{
    $user = UserDataManager :: get_instance()->retrieve_user(Session :: get_user_id());

    if (strpos($_SERVER['REQUEST_URI'], 'leave.php') === false && strpos($_SERVER['REQUEST_URI'], 'ajax') === false)
    {
        $return = Event :: trigger('enter', UserManager :: APPLICATION_NAME, array(VisitTracker :: PROPERTY_LOCATION => $_SERVER['REQUEST_URI'], VisitTracker :: PROPERTY_USER_ID => $user->get_id()));
        $htmlHeadXtra[] = '<script type="text/javascript">var tracker=' . $return[0]->get_id() . ';</script>';
    }
}

$htmlHeadXtra[] = '<script type="text/javascript">var rootWebPath="' . Path :: get(WEB_PATH) . '";</script>';

$timezone = LocalSetting :: get('platform_timezone');
date_default_timezone_set($timezone);

/**
 * Dump functionality with decent output
 */
function dump($variable)
{
    echo '<pre style="background-color: white; color: black; padding: 5px; margin: 0px;">';
    print_r($variable);
    echo '</pre>';
}

/**
 * Globaly available function that call the DebugUtilities :: show() static function
 *
 * @param mixed $object The object to print in the page
 * @return void
 */
function debug($object, $title = null, $backtrace_index = 1)
{
    DebugUtilities :: show($object, $title, $backtrace_index);
}

/**
 * Error handling function
 */
function handle_error($errno, $errstr, $errfile, $errline)
{
    switch ($errno)
    {
        case E_USER_ERROR :
            write_error('PHP Fatal error', $errstr, $errfile, $errline);
            break;
        case E_USER_WARNING :
            write_error('PHP Warning', $errstr, $errfile, $errline);
            break;
        case E_USER_NOTICE :
            write_error('PHP Notice', $errstr, $errfile, $errline);
    }

    return true;
}

function write_error($errno, $errstr, $errfile, $errline)
{
    $path = Path :: get(SYS_FILE_PATH) . 'logs';
    $file = $path . '/error_log_' . date('Ymd') . '.txt';
    $fh = fopen($file, 'a');

    $message = date('[H:i:s] ', time()) . $errno . ' File: ' . $errfile . ' - Line: ' . $errline . ' - Message: ' . $errstr;

    fwrite($fh, $message . "\n");
    fclose($fh);
}
?>