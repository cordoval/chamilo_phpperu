<?php
/**
 * $Id: old_migration_data_manager.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib
 * @author Van Wayenbergh David
 * @author Vanpoucke Sven
 */
abstract class OldMigrationDataManager
{

    abstract function validate_settings();

    abstract function move_file($old_rel_path, $new_rel_path, $filename);

    abstract function create_directory($is_new_system, $rel_path);

    abstract function append_full_path($is_new_system, $rel_path);
    
    private static $instance;

    /**
     * Constructor.
     */
    protected function OldMigrationDataManager()
    {
        $this->initialize();
    }

    /**
     * Singleton and factory pattern in one
     */
    static function getInstance($platform, $old_directory)
    {
        if (! isset(self :: $instance))
        {
            $filename = dirname(__FILE__) . '/../platform/' . strtolower($platform) . '/' . strtolower($platform) . '_data_manager.class.php';
            if (! file_exists($filename) || ! is_file($filename))
            {
                echo ($filename);
                die('Failed to load ' . $platform . '_data_manager.class.php');
            }
            $class = $platform . 'DataManager';
            require_once $filename;
            
            self :: $instance = new $class($old_directory);
        }
        
        return self :: $instance;
    }
}
?>