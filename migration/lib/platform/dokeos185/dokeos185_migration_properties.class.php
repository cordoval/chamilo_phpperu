<?php

require_once dirname(__FILE__) . '/dokeos185_data_manager.class.php';

class Dokeos185MigrationProperties extends MigrationProperties
{
	/**
	 * The dokeos 185 configuration array
	 * @var String[]
	 */
	private $configuration;
	
	/**
	 * The datamanager for this platform
	 */
	private $data_manager;
	
	function Dokeos185MigrationProperties()
	{
		
	}
	
	/**
	 * Validates the settings of the migration
	 * @param $settings - The general settings
	 * @param $blocks - The selected blocks
	 */
	function validate_settings($settings, $blocks)
	{
		$succes = $this->check_system_availability($settings);
		$succes &= $this->validate_blocks($blocks);
		
		return $succes;
	}
	
	/**
	 * Checks the availability of the system
	 * @param String[] $settings
	 */
	function check_system_availability($settings)
	{
		$configuration = $this->get_configuration();
		if(!$configuration)
		{
			return false;
		}	
		
		try
		{
			$data_manager = $this->get_data_manager();
		}
		catch(Exception $e)
		{
			return false;
		}
		
		return true;
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
     * Creates a new instance of the dokeos 1.8.5 data manager
     */
    function get_data_manager()
    {
    	if(!$this->data_manager)
    	{
    		$this->data_manager = new Dokeos185DataManager($this->get_configuration());
    	}
    	
    	return $this->data_manager;
    }
	
	/**
	 * Validates every block
	 * Checks if every block his prerequisite is selected as well
	 * @param String[] $blocks - The selected blocks
	 */
	function validate_blocks($blocks)
	{
		foreach($blocks as $block)
		{
			$class = Utilities :: underscores_to_camelcase($block) . 'MigrationBlock';
			$object = new $class();
			if(!$object->check_prerequisites($blocks))
			{
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * We need to define this manually because the order of the migration blocks is of very big importance for the prerequisites of some blocks
	 */
	function get_migration_blocks()
	{
		$this->require_migration_blocks();
		
		$blocks = array(UsersMigrationBlock :: MIGRATION_BLOCK_NAME, ClassesMigrationBlock :: MIGRATION_BLOCK_NAME, PersonalAgendasMigrationBlock :: MIGRATION_BLOCK_NAME, 
					    SettingsMigrationBlock :: MIGRATION_BLOCK_NAME, CoursesMigrationBlock :: MIGRATION_BLOCK_NAME, CourseAnnouncementsMigrationBlock :: MIGRATION_BLOCK_NAME,
					    CourseAssignmentsMigrationBlock :: MIGRATION_BLOCK_NAME, CourseCalendarEventsMigrationBlock :: MIGRATION_BLOCK_NAME, CourseDocumentsMigrationBlock :: MIGRATION_BLOCK_NAME,
					    CourseDropboxesMigrationBlock :: MIGRATION_BLOCK_NAME, CourseForumsMigrationBlock :: MIGRATION_BLOCK_NAME, CourseGroupsMigrationBlock :: MIGRATION_BLOCK_NAME,
					    CourseLearningPathsMigrationBlock :: MIGRATION_BLOCK_NAME, CourseLinksMigrationBlock :: MIGRATION_BLOCK_NAME, CourseMetaDataMigrationBlock :: MIGRATION_BLOCK_NAME,
					    CourseQuizzesMigrationBlock :: MIGRATION_BLOCK_NAME, CourseScormsMigrationBlock :: MIGRATION_BLOCK_NAME, CourseStudentPublicationsMigrationBlock :: MIGRATION_BLOCK_NAME,
					    CourseSurveysMigrationBlock :: MIGRATION_BLOCK_NAME, TrackersMigrationBlock :: MIGRATION_BLOCK_NAME);

		return $blocks;
	}
	
	function require_migration_blocks()
	{
		$dir = dirname(__FILE__) . '/migration_block/';
    	
    	if(!file_exists($dir))
    	{
    		return;
    	}
    	
    	$files = Filesystem :: get_directory_content($dir, Filesystem :: LIST_FILES, false);
    	
    	foreach($files as $file)
    	{
    		require_once($dir . $file);
    	}
	}
}