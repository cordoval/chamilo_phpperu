<?php

/**
 * Class to start the migration of the users
 * @author vanpouckesven
 *
 */
class UsersMigrationBlock extends MigrationBlock
{
	const MIGRATION_BLOCK_NAME = 'users';
	
	function get_prerequisites()
	{
		return array();
	}
	
	function get_block_name()
	{
		return self :: MIGRATION_BLOCK_NAME;
	}
}