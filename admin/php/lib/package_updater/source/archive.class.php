<?php
require_once Path :: get_admin_path() . 'lib/package_updater/package_updater_source.class.php';

class PackageUpdaterArchiveSource extends PackageUpdaterSource
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