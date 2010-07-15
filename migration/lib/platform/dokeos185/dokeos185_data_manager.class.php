<?php

/**
 * Class that connects to the old dokeos185 system
 *
 * @author Sven Vanpoucke
 * @author David Van Wayenbergh
 */
class Dokeos185DataManager extends MigrationDatabase implements PlatformMigrationDataManager
{
	/**
	 * The dokeos 185 configuration array
	 * @var String[]
	 */
	private $configuration;

	/**
	 * Variable to keep track of the selected database;
	 * @var String
	 */
	private $current_database;
	
	/**
	 * Singleton
	 */
	private static $instance;
	
	static function get_instance()
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
		
		$connection_string = 'mysql://' . $this->configuration['db_user'] . ':' . $this->configuration['db_password'] . '@' . $this->configuration['db_host'] . '/' . $this->get_database_name('main_database');
		$this->initialize($connection_string);
		$this->current_database = $this->get_database_name('main_database');
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
        
    	if($this->current_database == $database_name)
        {
        	return;
        }
        
    	$this->current_database = $database_name;
        $this->get_connection()->setDatabase($database_name);
    }
    
    /**
     * Retrieve all objects
     * @param Dokeos185MigrationDataClass $data_class
     * @param int $offset - the offset
     * @param int $count - the number of objects to retrieve 
     */
    function retrieve_all_objects($data_class, $offset, $count)
    {
    	$this->set_database($data_class->get_database_name());
    	return $this->retrieve_objects_with_condition($data_class, null, $offset, $count);
    }
    
    /**
     * Counts all objects
     * @param Dokeos185MigrationDataClass $data_class 
     */
    function count_all_objects($data_class)
    {
    	$this->set_database($data_class->get_database_name());
    	return $this->count_objects($data_class->get_table_name());
    }
    
    /**
     * Retrieve all objects with use of a condition
     * @param Dokeos185MigrationDataClass $data_class
     * @param Condition $condition
     * @param int $offset - the offset
     * @param int $count - the number of objects to retrieve 
     */
    function retrieve_objects_with_condition($data_class, $condition, $offset, $count)
    {
    	$this->set_database($data_class->get_database_name());
    	return $this->retrieve_objects($data_class->get_table_name(), null, $offset, $count, null, $data_class->get_class_name());
    }
    
    /**
     * Check wether a user is a platform admin
     */
    function is_platform_admin($user)
    {
    	$condition = new EqualityCondition(Dokeos185User :: PROPERTY_USER_ID, $user->get_user_id());
    	$count = $this->count_objects('admin', $condition);
    	
    	return ($count > 0);
    }
    
    /**
     * Gets the system path of the dokeos185 installation
     */
    function get_sys_path()
    {
    	$conf = $this->get_configuration();
    	return $conf['root_sys'];
    }
    
}

?>