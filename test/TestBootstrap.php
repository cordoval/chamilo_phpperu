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


class Initializer
{
    /**
     * Initialize the environment 
     */
    public static function init()
    {
        self::_initDefaultTimezone();
        self::_initIncludePath();
        self::_initAutoload();
        self::_initPHPSettings();
    }



    /**
     * Initialize the default timezone which is mandatory for PHP 5
     */
    private static function _initDefaultTimezone()
    {
        date_default_timezone_set('UTC');
    }

    /**
     * Initialize the PHP include_path
     */
    private static function _initIncludePath()
    {
        $pearPath = realpath(Path :: get_plugin_path() . 'pear');
        $googleLibraryPath = realpath(Path :: get_plugin_path() . 'google/library');

        $path = array(
            $pearPath,
            $googleLibraryPath
        );


        set_include_path(implode(PATH_SEPARATOR, $path) .  get_include_path());

    }


    private static function _initAutoload()
    {
        spl_autoload_register('common\libraries\Utilities::autoload');
    }

    private static function _initPHPSettings()
    {
        ini_set('error_reporting', 'E_ALL & ~E_DEPRECATED');
    }

}

Initializer::init();
