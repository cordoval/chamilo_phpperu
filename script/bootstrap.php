<?php
class ScriptInitializer
{
    private $bundled_libraries_path;

    public function  __construct() {
        $bundled_libraries_path = __DIR__ . '/lib';
    }

    /**
     * Initialize the environment
     */
    public function init()
    {
        $this->initDefaultTimezone();
        $this->initIncludePath();
        $this->initPHPSettings();
        $this->initEnv();
    }

    /**
     * Initialize the default timezone which is mandatory for PHP 5
     */
    private function initDefaultTimezone()
    {
        date_default_timezone_set('UTC');
    }

    /**
     * Initialize the PHP include_path
     */
    private function initIncludePath()
    {
        $bundledPath = realpath(__DIR__ . '/lib');
        $include_path = $bundledPath . PATH_SEPARATOR . get_include_path();
        set_include_path($include_path);
        ini_set('include_path', $include_path);
    }

    private function initPHPSettings()
    {
        ini_set('html_errors', 'off');
    }

    private function initEnv()
    {
        putenv("PHING_HOME=" . __DIR__ ."/lib/phing");
    }

}

$init = new ScriptInitializer();
$init->init();
unset($init);
