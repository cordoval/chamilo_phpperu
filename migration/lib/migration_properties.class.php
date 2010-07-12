<?php
/**
 * General migration properties class that describes the properties for a platform
 */

abstract class MigrationProperties
{
	/**
	 * Factory method for properties 
	 * @param String $platform - the selected platform
	 * @return MigrationProperties
	 */	
	function factory($platform)
	{
		$file = dirname(__FILE__) . '/platform/' . $platform . '/' . $platform . '_migration_properties.class.php';
		if(!file_exists($file))
		{
			throw new Exception(Translation :: get('CanNotFindMigrationProperties', array('PLATFORM' => $platform)));
		}	
		
		require_once($file);
		
		$class = Utilities :: underscores_to_camelcase($platform) . 'MigrationProperties';
		
		return new $class();
	}
	
	abstract function validate_settings($settings, $blocks);
	abstract function get_migration_blocks();
}