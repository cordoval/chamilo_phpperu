<?php

require_once dirname(__FILE__) . '/../data_class/dokeos185_personal_agenda.class.php';

/**
 * Class to start the migration of the personal agendas
 * @author vanpouckesven
 *
 */
class PersonalAgendasMigrationBlock extends MigrationBlock
{
	const MIGRATION_BLOCK_NAME = 'personal_agendas';
	
	function get_prerequisites()
	{
		return array(UsersMigrationBlock :: MIGRATION_BLOCK_NAME);
	}
	
	function get_data_classes()
	{
		return array(new Dokeos185PersonalAgenda);
	}
	
	function get_block_name()
	{
		return self :: MIGRATION_BLOCK_NAME;
	}
}

?>