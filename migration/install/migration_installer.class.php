<?php
/**
 * $Id: migration_installer.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package migration.install
 */
/**
 * This installer can be used to create the storage structure for the
 * migration application.
 */
class MigrationInstaller extends Installer
{
    /**
     * Constructor
     */
    function MigrationInstaller($values)
    {
        parent :: __construct($values, MigrationDataManager :: get_instance());
    }

	function get_path() 
	{
		return dirname(__FIlE__);
	}

}
?>