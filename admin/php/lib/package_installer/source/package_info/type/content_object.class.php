<?php
namespace admin;
use common\libraries\Path;
require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_type.class.php';

/**
 * $Id: content_object.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.package_installer.type
 */

class ContentObjectPackageInfo extends PackageInfo
{
	function get_path()
    {
    	return Path :: get_repository_content_object_path() . $this->get_package_name() . '/php/';
    }
}
?>