<?php
require_once Path :: get_admin_path() . 'lib/package_updater/package_updater_dependency.class.php';

class PackageUpdaterExtensionsDependency extends PackageUpdaterDependency
{
    function check($dependency)
    {
        $message = Translation :: get('DependencyCheckextension') . ': ' . $dependency['id'];
        $this->add_message($message);
        
        return extension_loaded($dependency['id']);
    }
}
?>