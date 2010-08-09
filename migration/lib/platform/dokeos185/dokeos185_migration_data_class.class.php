<?php
/**
 * $Id: import.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager
 */

require_once dirname(__FILE__) . '/dokeos185_data_manager.class.php';

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

    /**
     *
     * @return Dokeos185DataManager
     */
    function get_data_manager()
    {
    	return Dokeos185DataManager :: get_instance();
    }
    
	/**
	 * Creates a failed element object
	 * @param Int $id
	 */
	function create_failed_element($id, $table = null)
	{
		if(!$table)
		{
			$table = $this->get_database_name() . '.' . $this->get_table_name();
		}
		
		return parent :: create_failed_element($id, $table);
	}
	
	/**
	 * Creates an id reference object
	 * @param int $old_id
	 * @param int $new_id
	 */
	function create_id_reference($old_id, $new_id, $table = null)
	{
		if(!$table)
		{
			$table = $this->get_database_name() . '.' . $this->get_table_name();
		}
		
		return parent :: create_id_reference($old_id, $new_id, $table);
	}
    
	/**
	 * Retrieves a failed element
	 * @param Int $id
	 * @param String $table
	 */
	function get_failed_element($id, $table = null)
	{
		if(!$table)
		{
			$table = $this->get_database_name() . '.' . $this->get_table_name();
		}
		
		return parent :: get_failed_element($id, $table);
	}
	
	/**
	 * Retrieves an id reference
	 * @param Int $old_id
	 * @param String $table
	 */
	function get_id_reference($old_id, $table = null)
	{
		if(!$table)
		{
			$table = $this->get_database_name() . '.' . $this->get_table_name();
		}
		
		return parent :: get_id_reference($old_id, $table);
	}
	
    abstract function get_database_name();
    
}

?>