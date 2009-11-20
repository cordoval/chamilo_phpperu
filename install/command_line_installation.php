<?php
/**
 * $Id: command_line_installation.php 225 2009-11-13 14:43:20Z vanpouckesven $
 * @package install.lib
 */

session_start();

$cidReset = true;
$this_section = 'install';

ini_set("memory_limit", "-1"); // Geen php-beperkingen voor geheugengebruik
ini_set("max_execution_time", "7200"); // Twee uur moet voldoende zijn...
ini_set("error_reporting", "E_ALL & ~E_NOTICE");

require_once dirname(__FILE__) . '/../common/filesystem/path.class.php';
require_once dirname(__FILE__) . '/../common/utilities.class.php';
ini_set('include_path', realpath(Path :: get_plugin_path() . 'pear'));

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

Filesystem :: remove(dirname(__FILE__) . '/../common/configuration/configuration.php');

require_once dirname(__FILE__) . '/lib/install_manager/install_manager.class.php'; 
require_once 'MDB2.php';

require_once dirname(__FILE__) . '/command_line_configuration.inc.php'; 

Request :: set_get('install_running', 1);

Translation :: set_application($this_section);
Translation :: set_language('english');

// Functions


function create_database()
{
    global $values;

    $connection_string = $values['database_driver'] . '://' . $values['database_username'] . ':' . $values['database_password'] . '@' . $values['database_host'];
    $connection = MDB2 :: connect($connection_string);

    if (MDB2 :: isError($connection))
    {
        return array(Installer :: INSTALL_SUCCESS => false, Installer :: INSTALL_MESSAGE => (Translation :: get('DBConnectError') . $connection->getMessage()));
    }
    else
    {
        $drop_query = 'DROP DATABASE IF EXISTS ' . $values['database_name'];
        $drop_result = $connection->exec($drop_query);
        if (! MDB2 :: isError($drop_result))
        {
            $create_query = 'CREATE DATABASE IF NOT EXISTS ' . $values['database_name'] . ' DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci';
            $create_result = $connection->exec($create_query);
            if (! MDB2 :: isError($create_result))
            {
            	return array(Installer :: INSTALL_SUCCESS => true, Installer :: INSTALL_MESSAGE => Translation :: get('DBCreated'));
            }
            else
            {
                return array(Installer :: INSTALL_SUCCESS => false, Installer :: INSTALL_MESSAGE => (Translation :: get('DBCreateError') . ' ' . mysql_error()));
            }
        }
        else
        {
            return array(Installer :: INSTALL_SUCCESS => false, Installer :: INSTALL_MESSAGE => (Translation :: get('DBDropError') . ' ' . mysql_error()));
        }
    }
}

function create_folders()
{
    $files_path = dirname(__FILE__) . '/../files/';

    $directories = array('archive', 'fckeditor', 'garbage', 'repository', 'temp', 'userpictures');
    foreach ($directories as $directory)
    {
        $path = $files_path . $directory;
        if (! Filesystem :: create_dir($path))
        {
            return array(Installer :: INSTALL_SUCCESS => false, Installer :: INSTALL_MESSAGE => Translation :: get('FoldersCreatedFailed'));
        }
    }
    return array(Installer :: INSTALL_SUCCESS => true, Installer :: INSTALL_MESSAGE => Translation :: get('FoldersCreatedSuccess'));
}

function write_config_file()
{
    global $values;

    $content = file_get_contents('../common/configuration/configuration.dist.php');

    if ($content === false)
    {
        return array(Installer :: INSTALL_SUCCESS => false, Installer :: INSTALL_MESSAGE => Translation :: get('ConfigWriteFailed'));
    }

    $config['{DATABASE_DRIVER}'] = $values['database_driver'];
    $config['{DATABASE_HOST}'] = $values['database_host'];
    $config['{DATABASE_USER}'] = $values['database_username'];
    $config['{DATABASE_PASSWORD}'] = $values['database_password'];
    $config['{DATABASE_USERDB}'] = $values['database_user'];
    $config['{DATABASE_NAME}'] = $values['database_name'];
    $config['{ROOT_WEB}'] = $values['platform_url'];
    $config['{ROOT_SYS}'] = str_replace('\\', '/', realpath($values['platform_url']) . '/');
    $config['{SECURITY_KEY}'] = md5(uniqid(rand() . time()));
    $config['{URL_APPEND}'] = $values['url_append'];
    $config['{HASHING_ALGORITHM}'] = $values['hashing_algorithm'];
    
    foreach ($config as $key => $value)
    {
        $content = str_replace($key, $value, $content);
    }

    $fp = fopen('../common/configuration/configuration.php', 'w');

    if ($fp !== false)
    {

        if (fwrite($fp, $content))
        {
            fclose($fp);
            return array(Installer :: INSTALL_SUCCESS => true, Installer :: INSTALL_MESSAGE => Translation :: get('ConfigWriteSuccess'));
        }
        else
        {
            return array(Installer :: INSTALL_SUCCESS => false, Installer :: INSTALL_MESSAGE => Translation :: get('ConfigWriteFailed'));
        }
    }
    else
    {
        return array(Installer :: INSTALL_SUCCESS => false, Installer :: INSTALL_MESSAGE => Translation :: get('ConfigWriteFailed'));
    }
}

function install_applications()
{
    global $core_applications, $applications, $values;

    foreach ($core_applications as $core_application)
    {
        $installer = Installer :: factory($core_application, $values);
        $result = $installer->install();
        process_result($core_application, $result, $installer->retrieve_message());
        unset($installer);
        flush();
    }

    //flush();


    foreach ($applications as $application)
    {
        $toolPath = Path :: get_application_path() . 'lib/' . $application . '/install';
        if (is_dir($toolPath) && Application :: is_application_name($application))
        {
            $check_name = 'install_' . $application;
            if (isset($values[$check_name]) && $values[$check_name] == '1')
            {
                $installer = Installer :: factory($application, $values);
                $result = $installer->install();
                process_result($application, $result, $installer->retrieve_message());
                unset($installer, $result);
                flush();
            }
            //			else
        //			{
        //				// TODO: Does this work ?
        //				$application_path = dirname(__FILE__).'/../../application/lib/' . $application . '/';
        //				if (!Filesystem::remove($application_path))
        //				{
        //					$this->process_result($application, array(Installer :: INSTALL_SUCCESS => false, Installer :: INSTALL_MESSAGE => Translation :: get('ApplicationRemoveFailed')));
        //				}
        //				else
        //				{
        //					$this->process_result($application, array(Installer :: INSTALL_SUCCESS => true, Installer :: INSTALL_MESSAGE => Translation :: get('ApplicationRemoveSuccess')));
        //				}
        //			}
        }
        //flush();
    }
}

function post_process()
{
    // Post processing includes a.o.:
    // 1. Roles and rights
    // 2. Tracking
    // 3. Reporting
    // 4. "Various"
    // Check the installer class for a comprehensive list.
    // Class located at: ./common/installer.class.php


    global $core_applications, $applications, $values;

    // Post-processing for core applications
    foreach ($core_applications as $core_application)
    {
        $installer = Installer :: factory($core_application, $values);
        $result = $installer->post_process();

        process_result($core_application, $result, $installer->retrieve_message());

        unset($installer);
        flush();
    }

    // Post-processing for selected applications
    foreach ($applications as $application)
    {
        $toolPath = Path :: get_application_path() . 'lib/' . $application . '/install';
        if (is_dir($toolPath) && Application :: is_application_name($application))
        {
            $check_name = 'install_' . $application;
            if (isset($values[$check_name]) && $values[$check_name] == '1')
            {
                $installer = Installer :: factory($application, $values);
                $result = $installer->post_process();
                process_result($application, $result, $installer->retrieve_message());

                unset($installer, $result);
                flush();
            }
        }
        flush();
    }
}

function display_install_block_header($application)
{

    $html = array();
    $html[] = '';
    $html[] = Translation :: get(Application :: application_to_class($application));
    $html[] = '';

    return implode("\n", $html);
}

function process_result($application, $result, $message)
{
    echo display_install_block_header($application);
    echo strip_tags($message);
    echo "\n";
    if (! $result)
    {
        exit();
    }

}

// Available applications
$core_applications = array('webservice', 'admin', 'help', 'reporting', 'tracking', 'repository', 'user', 'group', 'rights', 'home', 'menu');
$applications = Filesystem :: get_directory_content(Path :: get_application_path() . 'lib/', Filesystem :: LIST_DIRECTORIES, false);

// Extra Configuration values


foreach ($applications as $application)
{
    $values['install_' . $application] = '1';
}

// 1. Create the database
$db_creation = create_database();
process_result('database', $db_creation['success'], $db_creation['message']);

// 2. Write the configuration file
$config_file = write_config_file();
process_result('config', $config_file['success'], $config_file['message']);



// 3. Installing the applications
install_applications();

// 4. Post-Processing all applications
post_process();

// 6. Create additional folders
$folder_creation = create_folders();
process_result('folder', $folder_creation['success'], $folder_creation['message']);

echo "\n";
?>
