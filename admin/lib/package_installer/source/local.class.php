<?php
require_once dirname(__FILE__) . '/package_info/package_info.class.php';

/**
 * $Id: local.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_installer.source
 */

class PackageInstallerLocalSource extends PackageInstallerSource
{

    function get_archive()
    {
    /**
     * Nothing to get since we're performing a local installation.
     */
    }

    function process()
    {
        $package_section = Request :: get(PackageManager :: PARAM_SECTION);
        $package_code = Request :: get(PackageManager :: PARAM_PACKAGE);
        $package_name = Utilities :: underscores_to_camelcase_with_spaces($package_code);
          
        $package_info = PackageInfo::factory($package_section, $package_code);
       
        $this->set_attributes($package_info->get_package());
        $this->get_parent()->add_message(Translation :: get('LocalPackageProcessed'));
        return true;
    }
}
?>