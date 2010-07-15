<?php
/**
 * $Id: migration_data_manager.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib
 * @author Van Wayenbergh David
 * @author Vanpoucke Sven
 */

/**
 * Abstract class from which the system datamanagers can extend
 * also used for communication with LCMS databases
 */
abstract class MigrationDataManager
{
    private static $instance;
    
    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return MigrationDataManagerInterface The implementation of the migration data manager interface
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '.class.php';
            $class = $type . 'MigrationDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    static function retrieve_migration_block_registrations_by_name($name)
    {
    	$condition = new EqualityCondition(MigrationBlockRegistration :: PROPERTY_NAME, $name);
    	return self :: get_instance()->retrieve_migration_block_registrations($condition)->next_result();
    }
    
    static function retrieve_failed_element_by_id_and_table($id, $table)
    {
    	$conditions = array();
    	$conditions[] = new EqualityCondition(FailedElement :: PROPERTY_FAILED_ID, $id);
    	$conditions[] = new EqualityCondition(FailedElement :: PROPERTY_FAILED_TABLE_NAME, $table);
    	$condition = new AndCondition($conditions);
    	
    	return self :: get_instance()->retrieve_failed_elements($condition)->next_result();
    }
    
	static function retrieve_id_reference_by_old_id_and_table($old_id, $table)
    {
    	$conditions = array();
    	$conditions[] = new EqualityCondition(IdReference :: PROPERTY_OLD_ID, $old_id);
    	$conditions[] = new EqualityCondition(IdReference :: PROPERTY_REFERENCE_TABLE_NAME, $table);
    	$condition = new AndCondition($conditions);
    	
    	return self :: get_instance()->retrieve_id_references($condition)->next_result();
    }
}

?>