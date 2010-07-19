<?php

require_once dirname(__FILE__) . '/../data_class/dokeos185_setting_current.class.php';
require_once dirname(__FILE__) . '/../data_class/dokeos185_system_announcement.class.php';

/**
 * Class to start the migration of the settings and system announcements
 * @author vanpouckesven
 *
 */
class SettingsMigrationBlock extends MigrationBlock
{
	const MIGRATION_BLOCK_NAME = 'settings';
	
	function get_prerequisites()
	{
		return array(UsersMigrationBlock :: MIGRATION_BLOCK_NAME);
	}
	
	function get_data_classes()
	{
		return array(new Dokeos185SettingCurrent(), new Dokeos185SystemAnnouncement());
	}
	
	function get_block_name()
	{
		return self :: MIGRATION_BLOCK_NAME;
	}
}

?>