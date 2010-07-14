<?php
/**
 * $Id: import.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager
 */

/**
 * Abstract import class
 * @author Sven Vanpoucke
 */
abstract class Dokeos185MigrationDataClass extends MigrationDataClass
{
	const PLATFORM = 'dokeos185';
    
    /**
     * Factory to retrieve the correct class of an old system
     * @param string $old_system the old system
     * @param string $type the class type
     */
	
    static function factory($type)
    {
        return parent :: factory(self :: PLATFORM, $type);
    }
    
    function get_data_manager()
    {
    	return Dokeos185DataManager :: get_instance();
    }
    
    abstract static function get_database_name();
    
}

?>