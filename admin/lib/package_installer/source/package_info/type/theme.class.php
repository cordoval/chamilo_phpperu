<?php
/**
 * $Id: theme.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.package_installer.type
 */

class ThemePackageInfo extends PackageInfo
{
    function get_path()
    {
    	return Path :: get_layout_path() . $this->get_package_name() . '/';
    }
}
?>