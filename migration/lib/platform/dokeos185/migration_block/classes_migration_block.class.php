<?php

require_once dirname(__FILE__) . '/../data_class/dokeos185_class.class.php';
require_once dirname(__FILE__) . '/../data_class/dokeos185_class_user.class.php';

/**
 * Class to start the migration of the classes
 * @author vanpouckesven
 *
 */
class ClassesMigrationBlock extends MigrationBlock
{
	const MIGRATION_BLOCK_NAME = 'classes';	
	
	function get_prerequisites()
	{
		return array(UsersMigrationBlock :: MIGRATION_BLOCK_NAME);
	}
	
	function get_data_classes()
	{
		return array(new Dokeos185Class(), new Dokeos185ClassUser());
	}
	
	function get_block_name()
	{
		return self :: MIGRATION_BLOCK_NAME;
	}
}

?>