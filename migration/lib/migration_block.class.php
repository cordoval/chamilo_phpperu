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
	
	function MigrationBlock()
	{
		$this->message_logger = MessageLogger :: get_instance(__CLASS__);
	}
	
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
	
	function migrate()
	{
		
	}
	
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
	
	abstract function get_prerequisites();
}