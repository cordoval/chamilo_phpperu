<?php
require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_source.class.php';

/**
 * $Id: archive.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_installer.source
 */

class PackageInstallerArchiveSource extends PackageInstallerSource
{

    function get_path()
    {
        return 'archive';
    }

	function get_archive()
	{
		
	}

}
?>