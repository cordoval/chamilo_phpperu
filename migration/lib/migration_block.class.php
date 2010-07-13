<?php
/**
 * General migration class to start a migration for a block
 */

abstract class MigrationBlock
{
	/**
	 * @var MessageLogger $message_logger used to log messages
	 */
	private $message_logger;
	
	/**
	 * The block registration
	 * @var MigrationBlockRegistration
	 */
	private $migration_block_registration;
	
	function MigrationBlock()
	{
		$this->message_logger = MessageLogger :: get_instance(__CLASS__);
	}
	
	// Messages function
	
	/**
	 * Returns the message logger
	 * @return MessageLogger
	 */
	function get_message_logger()
	{
		return $this->message_logger;
	}
	
	function render_message()
	{
		return $this->message_logger->render();
	}
	
	/**
	 * Factory method for migration block 
	 * @param String $platform - the selected platform
	 * @param String $block - the block name
	 * @return MigrationProperties
	 */	
	function factory($platform, $block)
	{
		$file = dirname(__FILE__) . '/platform/' . $platform . '/migration_block/' . $block . '_migration_block.class.php';
		if(!file_exists($file))
		{
			throw new Exception(Translation :: get('CanNotFindMigrationBlock', array('PLATFORM' => $platform, 'BLOCK' => $block)));
		}	
		
		require_once($file);
		
		$class = Utilities :: underscores_to_camelcase($block) . 'MigrationBlock';
		
		return new $class();
	}
	
	// Validation functions
	
	/**
	 * Checks the prerequisites of the current migration block against the selected migration blocks
	 * @param String[] $selected_blocks - The selected migration blocks 
	 */
	function check_prerequisites($selected_blocks)
	{
		$prerequisites = $this->get_prerequisites();
		foreach($prerequisites as $prerequisite)
		{
			if(!in_array($prerequisite, $selected_blocks))
			{
				return false;
			}
		}
		
		return true;
	}
	
	
	/**
	 * Migrate the current block
	 */
	function migrate()
	{
		$migration_block_registration = $this->get_migration_block_registration();
		if($migration_block_registration->get_is_migrated())
		{
			$this->message_logger->add_message(Translation :: get('MigrationBlockAlreadyMigrated'), MessageLogger :: TYPE_WARNING);
			return;
		}
		
		$this->update_migration_block_registration();
		$this->message_logger->add_message(Translation :: get('MigrationComplete'), MessageLogger :: TYPE_CONFIRM);
	}
	
	function update_migration_block_registration()
	{
		$migration_block_registration = $this->get_migration_block_registration();
		$migration_block_registration->set_is_migrated(1);
		$migration_block_registration->update();
	}
	
	function get_migration_block_registration()
	{
		if(!$this->migration_block_registration)
		{
			$this->migration_block_registration = MigrationDataManager :: retrieve_migration_block_registrations_by_name($this->get_block_name());
		}
		
		return $this->migration_block_registration;
	}
	
	// Abstract functions
	
	abstract function get_prerequisites();
	abstract function get_block_name();
}