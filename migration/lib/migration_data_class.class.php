<?php
/**
 * $Id: import.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager
 */

/**
 * Abstract migration data class
 * @author Sven Vanpoucke
 */
abstract class MigrationDataClass extends DataClass
{
	/**
	 * The failed / succes message of the validation / conversion
	 * @var String
	 */
	private $message;
	
	/**
	 * Returns the message
	 */
	function get_message()
	{
		return $this->message;
	}
	
	/**
	 * Sets the message
	 * @param String $message
	 */
	function set_message($message)
	{
		return $this->message = $message;
	}
	
	/**
	 * Some help functions
	 */
	
	/**
	 * Creates a failed element object
	 * @param Int $id
	 */
	function create_failed_element($id, $table = null)
	{
		$failed_element = new FailedElement();
		$failed_element->set_failed_id($id);
		
		if($table)
		{
			$failed_element->set_failed_table_name($table);
		}
		else
		{
			$failed_element->set_failed_table_name($this->get_table_name());
		}
		
		return $failed_element->create();
	}
	
	/**
	 * Creates an id reference object
	 * @param int $old_id
	 * @param int $new_id
	 */
	function create_id_reference($old_id, $new_id, $table = null)
	{
		$id_reference = new IdReference();
		$id_reference->set_old_id($old_id);
		$id_reference->set_new_id($new_id);
		
		if($table)
		{
			$id_reference->set_reference_table_name($table);
		}
		else
		{
			$id_reference->set_reference_table_name($this->get_table_name());
		}
		
		return $id_reference->create();
	}
	
	/**
	 * Creates a file recovery object
	 * @param String $old_file
	 * @param String $new_file
	 */
	function create_file_recovery($old_file, $new_file)
	{
		$file_recovery = new FileRecovery();
		$file_recovery->set_old_path($old_file);
		$file_recovery->set_new_path($new_file);
		return $file_recovery->create();
	}
	
    /**
     * Factory to retrieve the correct class of an old system
     * @param string $old_system the old system
     * @param string $type the class type
     */
    static function factory($old_system, $type)
    {
        $filename = dirname(__FILE__) . '/../platform/' . $old_system . '/' . $old_system . '_' . $type . '.class.php';
        
        if (! file_exists($filename) || ! is_file($filename))
        {
            echo ($filename);
            die('Failed to load ' . $filename);
        }
        $class = Utilities :: underscores_to_camelcase($old_system . '_' . $type);
        
        require_once $filename;
        return new $class();
    }
    
    /**
     * Checks wether the current data is valid
     */
    abstract function is_valid();

    /**
     * Converts the current data to chamilo 2.0 data
     */
    abstract function convert_data();
}

?>