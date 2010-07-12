<?php

class PersonalAgendasMigrationBlock extends MigrationBlock
{
	const MIGRATION_BLOCK_NAME = 'personal_agendas';
	
	function get_prerequisites()
	{
		return array(UsersMigrationBlock :: MIGRATION_BLOCK_NAME);
	}
}

?>