<?php
/**
 * General migration class to start a migration for a block
 */

abstract class MigrationBlock extends MessagesObject
{
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