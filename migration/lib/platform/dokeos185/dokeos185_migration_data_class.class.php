<?php
/**
 * $Id: import.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager
 */

require_once dirname(__FILE__) . '/dokeos185_data_manager.class.php';
require_once dirname(__FILE__) . '/dokeos185_text_field_parser.class.php';

/**
 * Abstract import class
 * @author Sven Vanpoucke
 */
abstract class Dokeos185MigrationDataClass extends MigrationDataClass
{
	const PLATFORM = 'dokeos185';
	
	/**
	 * List of all the objects that are included
	 * @var int[] - ContentObjectId
	 */
	private $included_objects;
    
	function Dokeos185MigrationDataClass()
	{
		$this->included_objects = array();
	}
	
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
	
	/**
	 * Parse a text field with multiple parsers
	 * @param String $field
	 * @param String[] $types
	 */
	function parse_text_field($field, $types = array(Dokeos185TextFieldParser :: TYPE_IMAGE))
	{
		foreach($types as $type)
		{
			$parser = Dokeos185TextFieldParser :: factory($type);
			$field = $parser->parse($field);
			$this->included_objects = array_merge($this->included_objects, $parser->get_included_objects());
		}
		
		return $field;
	}
	
	/**
	 * Parse a text field with all parsers
	 * @param String field
	 */
	function parse_text_field_with_all_types($field)
	{
		return $this->parse($field, array(Dokeos185TextFieldParser :: TYPE_IMAGE, Dokeos185TextFieldParser :: TYPE_FLASH, Dokeos185TextFieldParser :: TYPE_AUDIO));
	}
	
	function set_included_objects($included_objects)
	{
		$this->included_objects = $included_objects;
	}
	
	function get_included_objects()
	{
		return $this->included_objects;
	}
	
    abstract function get_database_name();
    
}

?>