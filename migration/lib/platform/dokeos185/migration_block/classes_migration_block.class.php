<?php

class ClassesMigrationBlock extends MigrationBlock
{
	const MIGRATION_BLOCK_NAME = 'classes';	
	
	function get_prerequisites()
	{
		return array(UsersMigrationBlock :: MIGRATION_BLOCK_NAME);
	}
}

?>