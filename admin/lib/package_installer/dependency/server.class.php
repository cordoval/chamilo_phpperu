<?php
require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_dependency.class.php';

/**
 * $Id: server.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_installer.dependency
 */

class PackageInstallerServerDependency extends PackageInstallerDependency
{

    function check($dependency)
    {
        $message = Translation :: get('DependencyCheckServer') . ': ' . $dependency['id'] . '. ' . Translation :: get('Expecting') . ': ' . $dependency['version']['_content'] . ' ' . Translation :: get('Found') . ': ';
        
        switch ($dependency['id'])
        {
            case 'php' :
                $message .= phpversion();
                $this->add_message($message);
                return $this->version_compare($dependency['version']['type'], $dependency['version']['_content'], phpversion());
                break;
            default :
                return true;
        }
    }
}
?>