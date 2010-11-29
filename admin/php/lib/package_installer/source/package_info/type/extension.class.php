<?php
namespace admin;

use common\libraries\Path;

require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_type.class.php';

/**
 * $Id: language.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.package_installer.type
 */

class ExtensionPackageInfo extends PackageInfo
{

    function get_path()
    {
        return Path :: get_common_extensions_path() . $this->get_package_name() . '/' . Path :: CLASS_PATH . '/';
    }
}
?>