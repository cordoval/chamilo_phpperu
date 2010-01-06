<?php
/**
 * $Id: utilities.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.javascript.ajax
 */
require_once dirname(__FILE__) . '/../../global.inc.php';

$type = $_POST['type'];
$output = array();

switch ($type)
{
    // Retrieve platform paths
    case 'path' :
        $path = $_POST['path'];
        $output['path'] = Path :: get($path);
        break;
    
    // Retrieve the current theme
    case 'theme' :
        $output['theme'] = Theme :: get_theme();
        break;
    
    // Get a translation
    case 'translation' :
        $application = $_POST['application'];
        $string = $_POST['string'];
        Translation :: set_application($application);
        $output['translation'] = Translation :: get($string);
        break;
    
    // Get, set or clear a session variable
    case 'memory' :
        $action = $_POST['action'];
        
        switch ($action)
        {
            case 'set' :
                $variable = Request :: post('variable');
                $value = Request :: post('value');
                $_SESSION[$variable] = $value;
                break;
            
            case 'get' :
                $variable = Request :: post('variable');
                $output['value'] = $_SESSION[$variable];
                break;
            
            case 'clear' :
                $variable = Request :: post('variable');
                unset($_SESSION[$variable]);
                break;
            
            default :
                $variable = Request :: post('variable');
                $output['value'] = $_SESSION[$variable];
                break;
        }
        break;
    case 'platform_setting' :
    	$variable = Request :: post('variable');
		$application = Request :: post('application');
		$output['platform_setting'] = PlatformSetting :: get($variable, $application);
		break;
}

$output = (object) $output;

echo json_encode($output);
?>