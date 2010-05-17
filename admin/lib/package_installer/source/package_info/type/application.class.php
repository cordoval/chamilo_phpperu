<?php
require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_type.class.php';

/**
 * $Id: application.class.php 179 2009-11-12 13:51:39Z vanpouckesven $
 * @package admin.lib.package_installer.type
 */

class ApplicationPackageInfo extends PackageInfo
{

    function get_path()
    {
    	return BasicApplication::get_application_path($this->get_package_name());
    }
    
}
?>