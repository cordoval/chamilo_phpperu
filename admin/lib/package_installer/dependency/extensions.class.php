<?php
require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_dependency.class.php';

/**
 * $Id: extensions.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_installer.dependency
 */

class PackageInstallerExtensionsDependency extends PackageInstallerDependency
{

    function check($dependency)
    {
        $message = Translation :: get('DependencyCheckextension') . ': ' . $dependency['id'];
        $this->add_message($message);
        
        return extension_loaded($dependency['id']);
    }
}
?>