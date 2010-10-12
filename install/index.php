<?php

use common\libraries\Utilities;
use common\libraries\Application;
/**
 * $Id: index.php 225 2009-11-13 14:43:20Z vanpouckesven $
 * @package install.lib
 */
function dump($variable)
{
    echo '<pre style="background-color: white; color: black; padding: 5px; margin: 0px;">';
    print_r($variable);
    echo '</pre>';
}

try
{
    session_start();
    
    $cidReset = true;
    $this_section = 'install';
    
    require_once dirname(__FILE__) . '/../common/libraries/php/filesystem/path.class.php';
    require_once dirname(__FILE__) . '/../common/libraries/php/utilities.class.php';
    ini_set('include_path', realpath(Path :: get_plugin_path() . 'pear'));
    
    ini_set("memory_limit", "-1");
    ini_set("max_execution_time", "7200");
    //error_reporting(E_ALL);
    error_reporting(E_ALL & ~ E_NOTICE & ~ E_DEPRECATED & ~ E_WARNING);
    //ini_set('display_errors', '0');
    
    spl_autoload_register('Utilities::autoload_core');
    spl_autoload_register('Utilities::autoload_web');
    require_once dirname(__FILE__) . '/php/lib/install_manager/install_manager.class.php';
    
    Request :: set_get('install_running', 1);
    
    Translation :: set_application($this_section);
    Translation :: set_language('english');
    
    Application :: launch('install', null);
}
catch (Exception $exception)
{
    Display :: error_message($exception->getMessage());
}
?>