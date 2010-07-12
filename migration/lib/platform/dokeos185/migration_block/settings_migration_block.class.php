<?php

class SettingsMigrationBlock extends MigrationBlock
{
	const MIGRATION_BLOCK_NAME = 'settings';
	
	function get_prerequisites()
	{
		return array(UsersMigrationBlock :: MIGRATION_BLOCK_NAME);
	}
}

?>