<?php

class TrackersMigrationBlock extends MigrationBlock
{
	const MIGRATION_BLOCK_NAME = 'trackers';
	
	function get_prerequisites()
	{
		return array(UsersMigrationBlock :: MIGRATION_BLOCK_NAME);
	}
}

?>