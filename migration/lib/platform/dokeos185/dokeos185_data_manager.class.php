<?php

/**
 * Class that connects to the old dokeos185 system
 *
 * @author Sven Vanpoucke
 * @author David Van Wayenbergh
 */
class Dokeos185DataManager extends MigrationDatabase
{
	/**
	 * The dokeos 185 configuration array
	 * @var String[]
	 */
	private $configuration;
	
	function Dokeos185DataManager($configuration)
	{
		$this->configuration = $configuration;
		$connection_string = 'mysql://' . $configuration['db_user'] . ':' . $configuration['db_password'] . '@' . $configuration['db_host'] . '/' . $this->get_database_name('dokeos_main');
		$this->initialize($connection_string);
	}
	
	function get_database_name($database_name)
	{
		return isset($this->configuration[$database_name]) ? $this->configuration[$database_name] : $database_name;
	}
	
	function set_database($database_name)
    {
        $database_name = $this->get_database_name($database_name);
        $this->get_connection()->setDatabase($database_name);
    }
}

?>