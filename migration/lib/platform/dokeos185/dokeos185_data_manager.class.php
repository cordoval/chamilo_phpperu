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
	
	/**
	 * Singleton
	 */
	private static $instance;
	
	private static function get_instance()
	{
		if(!self :: $instance)
		{
			self :: $instance = new self();
		}
		
		return self :: $instance;
	}
	
	/**
	 * Constructor
	 */
	function Dokeos185DataManager()
	{
		$this->configuration = $this->get_configuration();
		
		if(!$this->configuration)
		{
			throw new Exception(Translation :: get('PlatformConfigurationCanNotBeFound'));
		}
		
		$connection_string = 'mysql://' . $this->configuration['db_user'] . ':' . $this->configuration['db_password'] . '@' . $this->configuration['db_host'] . '/' . $this->get_database_name('dokeos_main');
		$this->initialize($connection_string);
	}
	
	/**
	 * Retrieves the configuration from dokeos 1.8.5
	 */
	function get_configuration()
    {
        if(!$this->configuration)
        {
        	$platform_path = 'file://' . PlatformSetting :: get('platform_path', MigrationManager :: APPLICATION_NAME);
	
	        if (file_exists($platform_path) && is_dir($platform_path))
	        {
	            $config_file = $platform_path . '/main/inc/conf/configuration.php';
	            if (file_exists($config_file) && is_file($config_file))
	            {
	                $_configuration = array();
	            	require_once ($config_file);
	                $this->configuration = $_configuration;
	            }
	        }
        }
        
        return $this->configuration;
    }
	
    /**
     * Get the database name from the configuration or use the given one
     * @param String $database_name
     */
	function get_database_name($database_name)
	{
		return isset($this->configuration[$database_name]) ? $this->configuration[$database_name] : $database_name;
	}
	
	/**
	 * Change the database selection
	 * @param String $database_name
	 */
	function set_database($database_name)
    {
        $database_name = $this->get_database_name($database_name);
        $this->get_connection()->setDatabase($database_name);
    }
}

?>