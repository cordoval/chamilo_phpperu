<?php
/**
 * General migration class to start a migration for a block
 */

abstract class MigrationBlock
{
	/**
	 * @var MessageLogger $message_logger - used to log messages on the screen
	 */
	private $message_logger;
	
	/**
	 * @var FileLogger $file_logger - used to log messages in a file
	 */
	private $file_logger;
	
	/**
	 * @var Timer
	 */
	private $timer;
	
	/**
	 * The block registration
	 * @var MigrationBlockRegistration
	 */
	private $migration_block_registration;
	
	function MigrationBlock()
	{
		$this->message_logger = MessageLogger :: get_instance(__CLASS__);
		$this->timer = new Timer();
	}
	
	// Messages function
	
	/**
	 * Returns the message logger
	 * @return MessageLogger
	 */
	private function get_message_logger()
	{
		return $this->message_logger;
	}
	
	function render_message()
	{
		return $this->message_logger->render();
	}
	
	/**
	 * Returns the file logger
	 * @return FileLogger
	 */
	private function get_file_logger()
	{
		if(!$this->file_logger)
		{
			$dir = Path :: get(SYS_FILE_PATH) . '/logs/migration/';
			if(!file_exists($dir))
			{
				Filesystem :: create_dir($dir);
			}
			$this->file_logger = new FileLogger($dir . $this->get_block_name() . '.log');
		}
		return $this->file_logger;
	}
	
	/**
	 * Returns the timer
	 * @return Timer
	 */
	private function get_timer()
	{
		return $this->timer;
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
		
		$this->prepare_migration();
		$this->finish_migration();
	}
	
	/**
	 * Prepares the migration
	 * Logfiles & Messages
	 */
	private function prepare_migration()
	{
		$this->get_timer()->start();
		$logger = $this->get_file_logger();
		
		$message = Translation :: get('StartMigration');
		$this->message_logger->add_message($message);
		$logger->log_message($message);
	}
	
	/**
	 * Finish te migration process
	 * Change the block registration
	 * Logfiles & Messages
	 */
	private function finish_migration()
	{
		$logger = $this->get_file_logger();
		
		$migration_block_registration = $this->get_migration_block_registration();
		$migration_block_registration->set_is_migrated(1);
		$migration_block_registration->update();
		
		$this->get_timer()->stop();
		
		$message = Translation :: get('MigrationComplete', array('TIME' => $this->get_timer()->get_time_in_hours()));
		$this->message_logger->add_message($message, MessageLogger :: TYPE_CONFIRM);
		$logger->log_message($message);
		
		$logger->close_file();
	}
	
	/**
	 * Returns the migration block registration or retrieves it from the database if it doesn't exist
	 */
	private function get_migration_block_registration()
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