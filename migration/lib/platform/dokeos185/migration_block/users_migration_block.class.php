<?php

require_once dirname(__FILE__) . '/../data_class/dokeos185_user.class.php';
require_once dirname(__FILE__) . '/../data_class/dokeos185_class.class.php';

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
	
	function get_data_classes()
	{
		return array(new Dokeos185User());
	}
	
	function get_block_name()
	{
		return self :: MIGRATION_BLOCK_NAME;
	}
}