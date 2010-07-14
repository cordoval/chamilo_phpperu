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
    
    abstract function is_valid();

    abstract function convert_data();
}

?>