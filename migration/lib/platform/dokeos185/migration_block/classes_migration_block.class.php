<?php

class ClassesMigrationBlock extends MigrationBlock
{
	const MIGRATION_BLOCK_NAME = 'classes';	
	
	function get_prerequisites()
	{
		return array(UsersMigrationBlock :: MIGRATION_BLOCK_NAME);
	}
	
	function get_block_name()
	{
		return self :: MIGRATION_BLOCK_NAME;
	}
}

?>