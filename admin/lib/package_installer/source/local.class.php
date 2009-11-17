<?php
require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_source.class.php';

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
        
        $package = new RemotePackage();
        $package->set_section($package_section);
        $package->set_code($package_code);
        $package->set_name($package_name);
        $package->set_version('1.0.0');
        $package->set_dependencies(serialize(array()));
        
        $this->set_attributes($package);
        $this->get_parent()->add_message(Translation :: get('LocalPackageProcessed'));
        return true;
    }
}
?>