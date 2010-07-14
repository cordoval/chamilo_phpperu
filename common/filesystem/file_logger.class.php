<?php

/**
 * A class which can be used to log messages to a file
 * @author Vanpoucke Sven
 */
class FileLogger
{
	private $handle;
	
	/**
	 * Constructor
	 * @param String $file - The full path to the file
	 * @param Bool $append - create a new file, or append to existing one
	 */
	function FileLogger($file, $append = false)
	{
		$mode = $append ? 'a+' : 'w+';
		$this->open_file($file, $mode);
	}	
	
	/**
	 * Opens the given file
	 * @param $file - The full path to the file
	 * @param $mode - The mode to open the file
	 */
	function open_file($file, $mode)
	{
		$this->handle = fopen($file, $mode);
	}
	
	/**
	 * Closes the file handle
	 */
	function close_file()
	{
		fclose($this->handle);
	}
	
	/**
	 * Logs a message to the file
	 * @param String $message
	 * @param Bool $include_timestamp
	 */
	function log_message($message, $include_timestamp = true)
	{
		if($include_timestamp)
		{
			$message = $this->get_timestamp() . $message;
		}
		
		fwrite($this->handle, $message . "\n");
	}
	
	/**
	 * Get's the current timestamp
	 */
	function get_timestamp()
	{
		$timestamp = strftime("[%H:%M:%S] ", time());
        return $timestamp;
	}
}

?>