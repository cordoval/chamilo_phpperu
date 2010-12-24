<?php
/**
 * PHP version 5
 * 
 * @author Systho
 *
 */

require_once dirname(__FILE__) . '/../common/libraries/php/filesystem/path.class.php';
require_once dirname(__FILE__) . '/../common/libraries/php/utilities.class.php';

use common\libraries\Path;
use common\libraries\Utilities;


class TestInitializer
{
    /**
     * Initialize the environment 
     */
    public static function init()
    {
        self::initDefaultTimezone();
        self::initIncludePath();
        self::initAutoload();
        self::initPHPSettings();
        self::initServerGlobals();
    }



    /**
     * Initialize the default timezone which is mandatory for PHP 5
     */
    private static function initDefaultTimezone()
    {
        date_default_timezone_set('UTC');
    }

    /**
     * Initialize the PHP include_path
     */
    private static function initIncludePath()
    {
        $pearPath = realpath(Path :: get_plugin_path() . 'pear');
        $googleLibraryPath = realpath(Path :: get_plugin_path() . 'google/library');
        $scriptLibrariesPath = __DIR__ . '/../script/lib';

        $path = array(
            $pearPath,
            $googleLibraryPath,
            $scriptLibrariesPath,
        );
        $new_include_path = implode(PATH_SEPARATOR, $path) . PATH_SEPARATOR .  get_include_path();

        set_include_path($new_include_path);
    }


    private static function initAutoload()
    {
        spl_autoload_register('common\libraries\Utilities::autoload');
    }

    private static function initPHPSettings()
    {
	// I do not understand why but Fatal error aren't shown 
	// when setting these parameters
        // Sven: the error reporting parameters are constants and should not be defined between quotes.
        ini_set('error_reporting', E_ALL & ~E_DEPRECATED);
    }
    
    private static function initServerGlobals()
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['HTTPS'] = false;
        $_SESSION = array();
    }

}

TestInitializer::init();
