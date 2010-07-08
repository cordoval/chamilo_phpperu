<?php
/**
 * $Id: import.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager
 */

/**
 * Abstract import class
 * @author Sven Vanpoucke
 */
abstract class MigrationDataClass extends DataClass
{

    /**
     * Factory to retrieve the correct class of an old system
     * @param string $old_system the old system
     * @param string $type the class type
     */
    static function factory($old_system, $type)
    {
        $filename = dirname(__FILE__) . '/../platform/' . strtolower($old_system) . '/' . strtolower($old_system) . strtolower($type) . '.class.php';
        
        if (! file_exists($filename) || ! is_file($filename))
        {
            echo ($filename);
            die('Failed to load ' . $old_system . $type . '.class.php');
        }
        $class = $old_system . str_replace('_', '', $type);
        
        require_once $filename;
        return new $class();
    }
    
    abstract function is_valid();

    abstract function convert_data();

    abstract static function retrieve_data($parameters);
}

?>